<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\Model;

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

defined('_JEXEC') or die;

/**
 * Methods supporting a list of positions records.
 * @since 4.0.0
 */
class PositionsModel extends ListModel
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
            ->from($db->quoteName('#__volunteers_positions') . ' AS a');

        // Join over the users for the checked_out user.
        $query
            ->select('checked_out.name AS editor')
            ->join('LEFT', '#__users AS ' . $db->quoteName('checked_out') . ' ON checked_out.id = a.checked_out');

        // Filter by published state
        $state = $this->getState('filter.state');

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

        // Filter by published state
        $type = $this->getState('filter.type');

        if (is_numeric($type) && ($type > 0)) {
            $query->where('a.type = ' . (int) $type);
        }

        // Filter by acl state
        $acl = $this->getState('filter.acl');

        switch ($acl) {
            case 'edit_department':
                $query->where('a.edit_department = 1');
                break;
            case 'edit':
                $query->where('a.edit = 1');
                break;
            case 'create_report':
                $query->where('a.create_report = 1');
                break;
            case 'create_team':
                $query->where('a.create_team = 1');
                break;
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.title');
        $orderDirn = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
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
        $id .= ':' . $this->getState('filter.type');

        return parent::getStoreId($id);
    }

    /**
     * Method to auto-populate the model state.
     *
     * @return  void
     *
     * @note    Calling getState in this method will result in recursion.
     * @since   4.0.0
     */
    protected function populateState($ordering = 'a.title', $direction = 'asc')
    {
        // Load the filter state.
        $this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search'));
        $this->setState('filter.state', $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state'));
        $this->setState('filter.type', $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type'));
        $this->setState('filter.acl', $this->getUserStateFromRequest($this->context . '.filter.acl', 'filter_acl'));

        // Load the parameters.
        $params = ComponentHelper::getParams('com_volunteers');
        $this->setState('params', $params);

        // List state information.
        parent::populateState($ordering, $direction);
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
}
