<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 *
 * Delete Volunteer Profile on User Delete
 *
 * @package     Joomla! Volunteers
 *
 * @since       4.0.0
 */
class plgUserVolunteer extends CMSPlugin
{
    /**
     * Application object.
     *
     * @var    CMSApplication
     * @since  1.0
     */
    protected $app;

    /**
     * Application object.
     *
     * @var    JDatabaseDriver
     * @since  1.0.0
     */
    protected $db;

    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $autoloadLanguage = true;


    /**
     *
     * Remove corresponding volunteer data for deleted user
     *
     * @param $user
     * @param $success
     * @param $msg
     *
     * @return false
     *
     * @since 4.0.0
     */
    public function onUserAfterDelete($user, $success, $msg): bool
    {
        if (!$success) {
            return false;
        }


        $query      = $this->db->getQuery(true);
        $conditions = array($this->db->quoteName('user_id') . ' = ' . $this->db->quote($user['id']));
        $query->delete($this->db->quoteName('#__volunteers_volunteers'));
        $query->where($conditions);
        $this->db->setQuery($query);

        return $this->db->execute();
    }

    /**
     *
     * Checks to see if user being deleted was or is a Joomla! volunteer
     *
     * @param $user
     *
     * @return bool
     *
     * @since 4.0.0
     */
    public function onUserBeforeDelete($user): bool
    {
        $this->db = Factory::getContainer()->get('DatabaseDriver');


        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('members.id'))
            ->from($this->db->quoteName('#__volunteers_members', 'members'))
            ->leftJoin($this->db->quoteName('#__volunteers_volunteers') . ' AS volunteers ON ' . $this->db->quoteName('volunteers.id') . ' = ' . $this->db->quoteName('members.volunteer'))
            ->where($this->db->quoteName('volunteers.user_id') . ' = ' . $this->db->quote($user['id']));

        $volunteer = $this->db->setQuery($query)->loadResult();

        if ($volunteer) {
            $this->app->enqueueMessage(Text::sprintf('PLG_USER_VOLUNTEERS_CANNOT_DELETE', $user['name']), 'error');
            $this->app->redirect('index.php?option=com_users&view=users');
            jexit();
        }

        return true;
    }
}
