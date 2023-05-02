<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\Model;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\User\User;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use RuntimeException;
use stdClass;

/**
 * Volunteer model.
 * @since 4.0.0
 */
class VolunteerModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since 4.0.0
     */
    public $typeAlias = 'com_volunteers.volunteer';

    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * @since 4.0.0
     */
    protected $text_prefix = 'COM_VOLUNTEERS';

    /**
     * @var null  Item data
     * @since  4.0.0
     */
    protected mixed $item = null;

    /**
     * The fields containing an url, only those that we can check on a 200 code
     *
     * @var    array
     * @since 4.0.0
     */
    protected array $url_fields = ['website', 'github', 'twitter', 'crowdin', 'joomladocs', 'certification'];

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
        $form = $this->loadForm('com_volunteers.volunteer', 'volunteer', ['control' => 'jform', 'load_data' => $loadData]);

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
     * Method to get team data.
     *
     * @param   integer  $pk  The id of the team.
     *
     * @return  mixed  Data object on success, false on failure.
     * @since 4.0.0
     * @throws Exception
     */
    public function getItem($pk = null): stdClass
    {
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');


        if ($pk > 0) {
            try {
                $db    = $this->getDatabase();
                $query = $db->getQuery(true)
                    ->select($this->getState('item.select', 'a.*, user.name AS name'))
                    ->from('#__volunteers_volunteers AS a')
                    ->where('a.id = ' . (int) $pk);

                // Join on user table.
                $query->select('user.email AS email')
                    ->join('LEFT', '#__users AS ' . $db->quoteName('user') . ' on user.id = a.user_id');

                // Filter by published state.
                $published = $this->getState('filter.published');
                $archived  = $this->getState('filter.archived');

                if (is_numeric($published)) {
                    $query->where('(a.published = ' . (int) $published . ' OR a.published =' . (int) $archived . ')')
                        ->where('(c.published = ' . (int) $published . ' OR c.published =' . (int) $archived . ')');
                }

                $db->setQuery($query);

                $data = $db->loadObject();

                if (empty($data)) {
                    throw new Exception(Text::_('COM_VOLUNTEERS_ERROR_VOLUNTEER_NOT_FOUND'), 404);
                }

                // Check for published state if filter set.
                if (((is_numeric($published)) || (is_numeric($archived))) && (($data->published != $published) && ($data->published != $archived))) {
                    throw new Exception(Text::_('COM_VOLUNTEERS_ERROR_VOLUNTEER_NOT_FOUND'), 404);
                }

                // Make sure we have http:// or https://
                if ($data->website) {
                    $data->website = parse_url($data->website, PHP_URL_SCHEME) == '' ? 'http://' . $data->website : $data->website;
                }

                return $data;
            } catch (Exception $e) {
                throw new Exception(($e));
            }
        } else {
            //throw new Exception(Text::_('COM_VOLUNTEERS_ERROR_VOLUNTEER_NOT_FOUND'),404);
        }

        // Convert to the JObject before adding other data.
        $properties = $this->getTable()->getProperties(1);

        return ArrayHelper::toObject($properties, 'stdClass');
    }

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  Table  A Table object
     * @since 4.0.0
     * @throws Exception
     */
    public function getTable($name = 'Volunteer', $prefix = 'VolunteersTable', $options = []): Table
    {
        return parent::getTable($name, $prefix, $options);
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  array  The default data is an empty array.
     * @since 4.0.0
     * @throws Exception
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_volunteers.edit.volunteer.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_volunteers.volunteer', $data);

        return $data;
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
    protected function prepareTable($table)
    {
        $date = Factory::getDate();
        $user = Factory::getApplication()->getSession()->get('user');

        if (empty($table->getId())) {
            // Set the values

            // Set ordering to the last item if not set
            if (empty($table->get('ordering'))) {
                $db    = $this->getDatabase();
                $query = $db->getQuery(true)
                    ->select('MAX(ordering)')
                    ->from($db->quoteName('#__volunteers_volunteers'));

                $db->setQuery($query);
                $max = $db->loadResult();

                $table->set('ordering', $max + 1);
            } else {
                // Set the values
                $table->set('modified', $date->toSql());
                $table->set('modified_by', $user->id);
            }
        }

        // Increment the version number.
        $v = $table->get('version');
        $v++;
        $table->set('version', $v);
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
        // Check the url fields
        foreach ($data as $field => $value) {
            if (in_array($field, $this->url_fields)) {
                switch ($field) {
                    case 'github':
                        $url = 'https://github.com/' . $value;
                        break;

                    case 'twitter':
                        $url = 'https://twitter.com/' . $value;
                        break;

                    case 'certification':
                        $url = 'https://exam.joomla.org/directory/user/' . $value;
                        break;

                    case 'joomladocs':
                        $url = 'https://docs.joomla.org/User:' . $value;
                        break;

                    case 'crowdin':
                        $url = 'https://crowdin.com/profile/' . $value;
                        break;

                    default:
                        $url = $value;
                        break;
                }

                if ($value) {
                    try {
                        if ($field == 'certification') {
                            $this->checkCertification($url);
                        } else {
                            $this->checkLink($url);
                        }
                    } catch (RuntimeException $e) {
                        throw new Exception(sprintf('COM_VOLUNTEERS_ERROR_URL_INVALID', $url, ucfirst($field)));
                    }
                }
            }
        }

        $app = Factory::getApplication();

        // Joomla User
        $dataUser = [
            'name'      => $data['name'],
            'username'  => PunycodeHelper::emailToPunycode($data['email']),
            'password'  => (isset($data['password1'])) ? $data['password1'] : '',
            'password2' => (isset($data['password2'])) ? $data['password2'] : '',
            'email'     => PunycodeHelper::emailToPunycode($data['email']),
        ];

        // Handle com_users changes
        if (isset($data['id'])) {
            $userId         = (int) $this->getItem($data['id'])->user_id;
            $dataUser['id'] = $userId;
            $user           = new User($userId);
        } else {
            $user                 = new User();
            $params               = ComponentHelper::getParams('com_users');
            $dataUser['groups'][] = $params->get('new_usertype', 2);
        }

        // Bind the data.
        if (!$user->bind($dataUser)) {
            throw new Exception("Could not bind data. Error: " . $user->getError());
        }

        // Store the data.
        if (!$user->save()) {
            throw new Exception("Could not save user. Error: " . $user->getError());
        }

        // Get User ID
        $data['user_id'] = $user->id;

        // Unset data
        unset($data['email']);
        unset($data['password1']);
        unset($data['password2']);

        // Set alias
        $data['alias'] = ApplicationHelper::stringURLSafe($data['name']);

        $return = parent::save($data);

        // Store the newly created volunteer ID
        $volunteerId = $this->getState('volunteer.id');
        $app->setUserState('com_volunteers.registration.id', $volunteerId);

        return $return;
    }

    /**
     * Method to check for valid certification link
     *
     * @param $url
     *
     * @return bool
     *
     * @since 1.0.1
     * @since 4.0.0
     */
    public function checkCertification($url): bool
    {
        // JHttp transport throws an exception when there's no response.
        try {
            $http     = HttpFactory::getHttp();
            $response = $http->get($url, [], 5);
        } catch (RuntimeException $e) {
            $response = null;
        }

        // Check for error text (most likely this will break at some point in the future...)
        if ((str_contains($response->body, 'You do not have any certifications.'))) {
            throw new RuntimeException();
        }

        return true;
    }

    /**
     * Method to check a link for a 200 or 301 response code
     *
     * @param $url
     *
     * @return bool
     *
     * @since 1.0.1
     * @since 4.0.0
     */
    public function checkLink($url): bool
    {
        // Adding a valid user agent string, otherwise some feed-servers returning an error
        $options = new Registry();
        $options->set('userAgent', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0');

        try {
            $response = HttpFactory::getHttp($options)->get($url, [], 5);
        } catch (RuntimeException $e) {
            $response = null;
        }

        // Check for response code
        if ($response->code !== 200 && $response->code !== 301) {
            throw new RuntimeException();
        }

        return true;
    }

    /**
     * getVolunteerId
     *
     * @param $userId
     *
     * @return mixed
     *
     * @since 4.0.0
     */
    public function getVolunteerId($userId = null): mixed
    {
        if (empty($userId)) {
            return -1;
        }

        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__volunteers_volunteers')
            ->where($db->quoteName('user_id') . ' = ' . (int) $userId);

        $db->setQuery($query);
        $id = $db->loadResult();

        if (!is_null($id)) {
            return $id;
        } else {
            return -1;
        }
    }

    /**
     * Method to get Department Members.
     *
     * @param   int|null  $pk  The id of the team.
     *
     * @return  mixed  Data object on success, false on failure.
     * @since 4.0.0
     * @throws Exception
     */
    public function getVolunteerTeams(int $pk = null): stdClass
    {
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

        // Get members
        $model = $this->getMVCFactory()->createModel('Members', 'Administrator', ['ignore_request' => true]);
        $model->setState('filter.volunteer', $pk);
        $items = $model->getItems();

        $teams               = new stdClass();
        $teams->active       = [];
        $teams->honorroll    = [];
        $teams->activemember = false;

        // Check for active or inactive members
        foreach ($items as $item) {
            if ($item->department && ($item->department_parent_id == 0)) {
                $item->link = Route::_('index.php?option=com_volunteers&view=board&id=' . $item->department);
                $item->name = $item->department_title;
            } elseif ($item->department) {
                $item->link = Route::_('index.php?option=com_volunteers&view=department&id=' . $item->department);
                $item->name = $item->department_title;
            } elseif ($item->team) {
                $item->link = Route::_('index.php?option=com_volunteers&view=team&id=' . $item->team);
                $item->name = $item->team_title;
            }

            if ($item->date_ended == '0000-00-00') {
                $teams->active[] = $item;
            } else {
                $teams->honorroll[] = $item;
            }

            if ($item->date_ended == '0000-00-00' && $item->position != 8) {
                $teams->activemember = true;
            }
        }

        return $teams;
    }
}
