<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\Model;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\String\StringHelper;

/**
 * Report model.
 * @since 4.0.0
 */
class ReportModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since 4.0.0
     */
    public $typeAlias = 'com_volunteers.report';

    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * @since 4.0.0
     */
    protected $text_prefix = 'COM_VOLUNTEERS';

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name    The table name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  Table  A Table object
     * @since 4.0.0
     * @throws Exception
     */
    public function getTable($name = 'Report', $prefix = 'VolunteersTable', $options = []): Table
    {
        return parent::getTable($name, $prefix, $options);
    }

    /**
     * Abstract method for getting the form from the model.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed  A Form object on success, false on failure
     * @since 4.0.0
     * @throws Exception
     */
    public function getForm($data = [], $loadData = true): Form
    {
        // Get the form.
        $form = $this->loadForm('com_volunteers.report', 'report', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        // Modify the form based on access controls.
        if (!$this->canEditState((object) $data)) {
            // Disable fields for display.
            $form->setFieldAttribute('ordering', 'disabled', 'true');
            $form->setFieldAttribute('state', 'disabled', 'true');

            // Disable fields while saving.
            $form->setFieldAttribute('ordering', 'filter', 'unset');
            $form->setFieldAttribute('state', 'filter', 'unset');
        }

        return $form;
    }

    /**
     * Method to get volunteer info.
     *
     * @return  mixed  Data object on success, false on failure.
     * @since 4.0.0
     * @throws Exception
     */
    public function getVolunteer(): mixed
    {
        // Get user
        $user = Factory::getApplication()->getIdentity();

        // Get subteams
        $model = $this->getMVCFactory()->createModel('Volunteer', 'Administrator', ['ignore_request' => true]);

        $volunteerId = $model->getVolunteerId($user->id);

        return $model->getItem($volunteerId);
    }

    /**
     * Prepare and sanitise the table data prior to saving.
     *
     * @param   Table  $table  A reference to a Table object.
     *
     * @return  void
     * @since 4.0.0
     * @throws Exception
     */
    protected function prepareTable($table): void
    {
        $date = Factory::getDate();
        $user = Factory::getApplication()->getIdentity();

        $table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
        $table->alias = ApplicationHelper::stringURLSafe($table->alias);

        if (empty($table->alias)) {
            $table->alias = ApplicationHelper::stringURLSafe($table->title);
        }

        if (empty($table->getId())) {
            // Set the values

            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $db    = $this->getDatabase();
                $query = $db->getQuery(true)
                    ->select('MAX(ordering)')
                    ->from($db->quoteName('#__volunteers_reports'));

                $db->setQuery($query);
                $max = $db->loadResult();

                $table->ordering = $max + 1;
            } else {
                // Set the values
                $table->modified    = $date->toSql();
                $table->modified_by = $user->id;
            }
        }

        // Increment the version number.
        $v  = $table->version;
        $v++;
        $table->version = $v;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success.
     * @since 4.0.0
     * @throws Exception
     */
    public function save($data): bool
    {
        $app = Factory::getApplication();

        // Alter the title for save as copy
        if ($app->input->get('task') == 'save2copy') {
            [$name, $alias]     = $this->generateNewTitle(0, $data['alias'], $data['title']);
            $data['title']      = $name;
            $data['alias']      = $alias;
            $data['state']      = 0;
        }

        return parent::save($data);
    }

    /**
     * Method to change the title & alias.
     *
     * @param   integer  $categoryId  The id of the parent.
     * @param   string   $alias       The alias.
     * @param   string   $title       The title.
     *
     * @return  array  Contains the modified title and alias.
     * @since 4.0.0
     * @throws Exception
     */
    protected function generateNewTitle($categoryId, $alias, $title): array
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(['alias' => $alias])) {
            if ($title == $table->title) {
                $title = StringHelper::increment($title);
            }

            $alias = StringHelper::increment($alias, 'dash');
        }

        return [$title, $alias];
    }





    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  array  The default data is an empty array.
     * @since 4.0.0
     * @throws Exception
     */
    protected function loadFormData(): array
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_volunteers.edit.report.data', []);
        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_volunteers.report', $data);

        return (array)$data;
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return  void
     *
     * @since   4.0.0
     *
     * @throws Exception
     */
    protected function populateState(): void
    {
        $app  = Factory::getApplication();
        $user = $app->getIdentity();

        // Check published state
        if ((!$user->authorise('core.edit.state', 'com_volunteers')) && (!$user->authorise('core.edit', 'com_volunteers'))) {
            $this->setState('filter.published', 1);
            $this->setState('filter.archived', 2);
        }

        // Load state from the request userState on edit or from the passed variable on default
        $id = $app->input->get('id');
        $app->setUserState('com_volunteers.edit.report.id', $id);

        $this->setState('report.id', $id);

        // Load the parameters.
        $params       = $app->getParams();
        $params_array = $params->toArray();

        if (isset($params_array['item_id'])) {
            $this->setState('report.id', $params_array['item_id']);
        }

        $this->setState('params', $params);
    }

    /**
     * Method to get team data.
     *
     * @param   int|null $pk  The id of the team.
     *
     * @return  mixed  Data object on success, false on failure.
     * @since 4.0.0
     * @throws Exception
     */
    public function getItem($pk = null): mixed
    {
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

        if ($pk > 0) {
            try {
                $db    = $this->getDatabase();
                $query = $db->getQuery(true)
                    ->select($this->getState('item.select', 'a.*'))
                    ->from('#__volunteers_reports AS a')
                    ->where('a.id = ' . (int) $pk);

                // Join on volunteer table.
                $query->select('volunteer.id AS volunteer_id, volunteer.image AS volunteer_image')
                    ->join('LEFT', '#__volunteers_volunteers AS ' . $db->quoteName('volunteer') . ' on volunteer.user_id = a.created_by');

                // Join over the users for the related user.
                $query
                    ->select('user.name AS volunteer_name')
                    ->join('LEFT', '#__users AS ' . $db->quoteName('user') . ' ON user.id = a.created_by');

                // Join on department table.
                $query->select('department.title AS department_title, department.parent_id AS department_parent_id')
                    ->join('LEFT', '#__volunteers_departments AS ' . $db->quoteName('department') . ' on department.id = a.department');

                // Join on team table.
                $query->select('team.title AS team_title')
                    ->join('LEFT', '#__volunteers_teams AS ' . $db->quoteName('team') . ' on team.id = a.team');

                // Filter by published state.
                $published = $this->getState('filter.published');
                $archived  = $this->getState('filter.archived');

                if (is_numeric($published)) {
                    $query->where('(a.state = ' . (int) $published . ' OR a.state =' . (int) $archived . ')');
                    //  ->where('(c.published = ' . (int) $published . ' OR c.published =' . (int) $archived . ')');
                }

                $db->setQuery($query);

                $data = $db->loadObject();

                if (empty($data)) {
                    throw new Exception(Text::_('COM_VOLUNTEERS_ERROR_REPORT_NOT_FOUND'), 404);
                }

                // Check for published state if filter set.
                if (((is_numeric($published)) || (is_numeric($archived))) && (($data->state != $published) && ($data->state != $archived))) {
                    throw new Exception(Text::_('COM_VOLUNTEERS_ERROR_REPORT_NOT_FOUND'), 404);
                }

                return $data;
            } catch (Exception $e) {
                throw new Exception($e);
            }
        }
        return null;
    }
}
