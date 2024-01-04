<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\Table;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table as Table;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\CMS\Tag\TaggableTableInterface;
use Joomla\CMS\Tag\TaggableTableTrait;
use Joomla\Database\DatabaseDriver;
use stdClass;

/**
 * Department Table class
 *
 * @since 4.0.0
 */
class DepartmentTable extends Table implements VersionableTableInterface, TaggableTableInterface
{
    use TaggableTableTrait;


    protected stdClass $myData;

    /**
     * Constructor
     *
     * @param   DatabaseDriver  $db  A database connector object
     *
     * @since 4.0.0
     */
    public function __construct(DatabaseDriver $db)
    {
        $this->myData    = new stdClass();
        $this->typeAlias = 'com_volunteers.department';
        parent::__construct('#__volunteers_departments', 'id', $db);

        // Set the published column alias
        $this->setColumnAlias('published', 'state');
    }

    /**
     * get
     *
     * @param   string  $property  lookup variable
     *
     * @return  mixed  variable looked up.
     *
     * @since 4.0.0
     */
    public function get($property, $default = null): mixed
    {
        return $this->myData->{$property};
    }

    /**
     * set
     *
     * @param   string  $property  lookup variable
     * @param   mixed  $value  set variable
     *
     * @since 4.0.0
     */
    public function set($property, $value = null): void
    {
        $this->myData->{$property} = $value;
    }


    /**
     * Overloaded check method to ensure data integrity.
     *
     * @return  boolean  True on success.
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function check(): bool
    {
        // check for valid name
        if (trim($this->get('title')) == '') {
            throw new Exception(Text::_('COM_VOLUNTEERS_ERR_TABLES_NAME'));
        }

        // Check for existing name

        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__volunteers_departments'))
            ->where($db->quoteName('title') . ' = ' . $db->quote($this->get('title')));
        $db->setQuery($query);

        $xid = (int) $db->loadResult();

        if ($xid && $xid != (int) $this->get('id')) {
            throw new Exception(Text::_('COM_VOLUNTEERS_ERR_TABLES_NAME'));
        }

        if (empty($this->get('alias'))) {
            $this->set('alias', $this->get('title'));
        }

        $this->set('alias', ApplicationHelper::stringURLSafe($this->get('alias')));

        if (trim(str_replace('-', '', $this->get('alias'))) == '') {
            $this->set('alias', Factory::getDate()->format("Y-m-d-H-i-s"));
        }

        return true;
    }

    /**
     * Get the type alias for the history table
     *
     * @return  string  The alias as described above
     *
     * @since   4.0.0
     */
    public function getTypeAlias(): string
    {
        return $this->typeAlias;
    }

    /**
     * Overload the store method for the table.
     *
     * @param   boolean  $updateNulls  Toggle whether null values should be updated.
     *
     * @return  boolean  True on success, false on failure.
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function store($updateNulls = false): bool
    {
        $date = Factory::getDate();
        $user = Factory::getApplication()->getIdentity();

        $this->set('modified', $date->toSql());

        if ($this->getId()) {
            // Existing item

            $this->set('modified_by', $user->id);
        } else {
            // New item. An item created and created_by field can be set by the user,
            // so we don't touch either of these if they are set.
            if (!(int) $this->get('created')) {
                $this->set('created', $date->toSql());
            }

            if (empty($this->get('created_by'))) {
                $this->set('created_by', $user->id);
            }
        }

        // Verify that the alias is unique
        $table = new DepartmentTable($this->getDbo());
        //$table = Table::getInstance('Department', 'VolunteersTable');

        if ($table->load(['alias' => $this->get('alias')]) && ($table->get('id') != $this->get('id') || $this->get('id') == 0)) {
            throw new Exception(Text::_('COM_VOLUNTEERS_ERROR_UNIQUE_ALIAS'));
        }

        return parent::store($updateNulls);
    }
}
