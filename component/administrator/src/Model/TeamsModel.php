<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\Model;

use Exception;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Database\QueryInterface;

defined('_JEXEC') or die;

/**
 * Methods supporting a list of teams records.
 * @since 4.0.0
 */
class TeamsModel extends ListModel
{
    protected array $_filters = [];
    protected bool $_code_model = false;

    /**
     * Allows extension code to instantiate models and pass filter and other parameters. If this is false then use State values.
     *
     * @param   bool  $is_model_being_used_by_code
     *
     *
     * @since version
     */
    public function setCodeModel(bool $is_model_being_used_by_code = false)
    {
        $this->_code_model = $is_model_being_used_by_code;
    }



    /**
     * Method to set state variables.
     *
     * @param   string  $property  The name of the property
     * @param   mixed   $value     The value of the property to set or null
     *
     * @return  mixed  The previous value of the property or null if not set
     *
     * @since   4.0.0
     */
    public function setState($property, $value = null): mixed
    {

        $this->_filters[$property] = $value;
        return parent::setState($property, $value);
    }

    /**
     * Method to get state variables.
     *
     * @param   string  $property  Optional parameter name
     * @param   mixed   $default   Optional default value
     *
     * @return  mixed  The property where specified, the state object where omitted
     *
     * @since   4.0.0
     */
    public function getState($property = null, $default = null): mixed
    {
        if ($this->_code_model) { // Use _filters not states
            return $this->_filters[$property] ?? $default;
        } else {
            return parent::getState($property, $default);
        }
    }
    /**
     * Constructor.
     *
     * @param   array  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   4.0.0
     * @throws Exception
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'alias', 'a.alias',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'state', 'a.state',
                'created', 'a.created',
                'created_by', 'a.created_by',
                'ordering', 'a.ordering',
                'featured', 'a.featured',
            );
        }

        parent::__construct($config);
        $this->populateState();
    }

    /**
     * Method to auto-populate the model state.
     *
     * @return  void
     *
     * @note    Calling getState in this method will result in recursion.
     * @since   4.0.0
     * @throws Exception
     */
    protected function populateState($ordering = 'a.title', $direction = 'asc')
    {
        // Load the filter state.
        $this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search'));
        $this->setState('filter.state', $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state'));
        $this->setState('filter.department', $this->getUserStateFromRequest($this->context . '.filter.department', 'filter_department'));
        $this->setState('filter.active', $this->getUserStateFromRequest($this->context . '.filter.active', 'filter_active'));
        $this->setState('filter.parent', $this->getUserStateFromRequest($this->context . '.filter.parent', 'filter_parent'));
        $this->setState('filter.groups', Factory::getApplication()->input->getInt('id'));

        // Load the parameters.
        $params = ComponentHelper::getParams('com_volunteers');
        $this->setState('params', $params);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string  $id  A prefix for the store id.
     *
     * @return  string  A store id.
     * @since 4.0.0
     */
    protected function getStoreId($id = ''): string
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $this->getState('filter.department');
        $id .= ':' . $this->getState('filter.active');
        $id .= ':' . $this->getState('filter.groups');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  QueryInterface
     * @since 4.0.0
     * @throws Exception
     */
    protected function getListQuery(): QueryInterface
    {
        // Create a new query object.
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select($this->getState('list.select', array('a.*')))
            ->from($db->quoteName('#__volunteers_teams') . ' AS a');

        // Join over the users for the checked_out user.
        $query
            ->select('checked_out.name AS editor')
            ->join('LEFT', '#__users AS ' . $db->quoteName('checked_out') . ' ON checked_out.id = a.checked_out');

        // Join over the departments.
        $query
            ->select('department.title AS department_title')
            ->join('LEFT', '#__volunteers_departments AS ' . $db->quoteName('department') . ' ON department.id = a.department');

        // Self-join over the parent team.
        $query
            ->select('parentteam.title AS parent_title')
            ->join('LEFT', '#__volunteers_teams AS ' . $db->quoteName('parentteam') . ' ON parentteam.id = a.parent_id');

        // Filter by published state
        $state = $this->getState('filter.state', 1);

        if (is_numeric($state)) {
            $query->where('a.state = ' . (int) $state);
        }

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
            }
        }

        // Filter by groups
        $groups = $this->getState('filter.groups');

        if (is_numeric($groups) && ($groups > 0)) {
            $query->where('a.department = ' . (int) $groups);
        } else {
            $query->where('a.department <> 58');
        }

        // Filter by department
        $department = $this->getState('filter.department');

        if (is_numeric($department) && ($department > 0)) {
            $query->where('a.department = ' . (int) $department);
        }

        // Filter by active state
        $frontend = Factory::getApplication()->isClient('site');
        $active = $this->getState('filter.active', ($frontend) ? 1 : null);

        if (is_numeric($active)) {
            $nullDate = $db->quote($db->getNullDate());

            if ($active == 1) {
                $query->where('a.date_ended = ' . $nullDate);
            }

            if ($active == 0) {
                $query->where('a.date_ended != ' . $nullDate);
            }
        }

        // Filter by subteams
        $subteams = $this->getState('filter.subteams');

        if (!$subteams) {
            $query->where('a.parent_id = 0');
        }

        // Filter by parent
        $parent = $this->getState('filter.parent');

        if (is_array($parent)) {
            $query->where('a.parent_id IN (' . implode($parent, ',') . ')');
        }

        if (is_numeric($parent) && ($parent > 0)) {
            $query->where('a.parent_id = ' . (int) $parent);
        }

        // Group by ID
        $query->group('a.id');

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.title');
        $orderDirn = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }

    /**
     * Method to get an array of data items.
     *
     * @return  array  An array of data items on success, false on failure.
     * @since 4.0.0
     * @throws Exception
     */
    public function getItems(): array
    {
        $items = parent::getItems();


        $teams   = array();
        $teamIds = array();

        foreach ($items as $item) {
            $teamIds[]                  = $item->id;
            $teams[$item->id]           = $item;
            $teams[$item->id]->members  = array();
            $teams[$item->id]->subteams = array();
        }

        // Get Subteams
        $subteams = $this->getSubteams();

        // Add Subteams
        foreach ($subteams as $subteam) {
            if (isset($teams[$subteam->parent_id])) {
                $teams[$subteam->parent_id]->subteams[] = $subteam;
            }
        }

        // Get members

        $members = new MembersModel();
        $members->setCodeModel(true);



        $members->setState('filter.active', 1);
        $members->setState('filter.type', 'team');
        $members->setState('filter.team', $teamIds);
        $members->setState('filter.private', 1);
        $members = $members->getItems();

        if (!empty($members)) {
            foreach ($members as $member) {
                if (isset($teams[$member->team])) {
                    $teams[$member->team]->members[] = $member;
                }
            }
        }

        return $teams;
    }

    /**
     * @param   null  $parent
     * @param   bool  $getmembers
     *
     * @return mixed
     *
     * @since version
     */
    public function getSubteams($parent = null, bool $getmembers = false)
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from('#__volunteers_teams');

        if (!$parent) {
            $query->where('parent_id > 0');
        }

        if (is_array($parent)) {
            $query->where('parent_id IN (' . implode($parent, ',') . ')');
        }

        if (is_numeric($parent) && ($parent > 0)) {
            $query->where('parent_id = ' . (int) $parent);
        }

        // Only active teams
        $nullDate = $db->quote($db->getNullDate());
        $query->where('date_ended = ' . $nullDate);

        $query->order('title ASC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }
}
