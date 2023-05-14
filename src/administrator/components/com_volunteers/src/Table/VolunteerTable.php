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
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table as Table;
use Joomla\CMS\Tag\TaggableTableInterface;
use Joomla\CMS\Tag\TaggableTableTrait;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;

/**
 * Volunteer Table class
 * @since 4.0.0
 */
class VolunteerTable extends Table implements VersionableTableInterface, TaggableTableInterface
{
    use TaggableTableTrait;

    /**
     * Constructor
     *
     * @param   DatabaseDriver  &$db  A database connector object
     *
     * @since 4.0.0
     */
    public function __construct(DatabaseDriver $db)
    {
        $this->typeAlias = 'com_volunteers.volunteers';
        parent::__construct('#__volunteers_volunteers', 'id', $db);

        // Set the published column alias
        $this->setColumnAlias('published', 'state');
    }

    /**
     * Method to bind the data.
     *
     * @param   array  $src     The data to bind.
     * @param   mixed  $ignore  An array or space separated list of fields to ignore.
     *
     * @return  boolean  True on success, false on failure.
     * @since 4.0.0
     */
    public function bind($src, $ignore = []): bool
    {
        // send_permission checkbox default
        if (!isset($src['send_permission'])) {
            $src['send_permission'] = 0;
        }

        // coc checkbox default
        if (!isset($src['coc'])) {
            $src['coc'] = 0;
        }

        return parent::bind($src, $ignore);
    }

    /**
     * Overloaded delete method
     *
     * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
     *
     * @return  boolean  True on success.
     * @since 4.0.0
     */
    public function delete($pk = null): bool
    {
        $return = parent::delete($pk);

        // Delete the Joomla User

        $user = Factory::getContainer()->get('user.factory')->loadUserById($this->get('user_id'));


        if (!$user->delete()) {
            return false;
        }

        return $return;
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
     * @param   boolean    Toggle whether null values should be updated.
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

            if (empty($this->created_by)) {
                $this->set('created_by', $user->id);
            }
        }

        // Birthday format
        if ($this->get('birthday') && $this->get('birthday') != '0000-00-00 00:00:00') {
            $this->set('birthday', Factory::getDate('0000-' . $this->get('birthday'))->format('Y-m-d'));
        }

        return parent::store($updateNulls);
    }
}
