<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseInterface;

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
class PlgUserVolunteer extends CMSPlugin
{
    /**
     * Application object.
     *
     * @var    DatabaseInterface
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
     * @param  array    $user
     * @param  boolean  $success
     * @param  string   $msg
     *
     * @return  boolean
     *
     * @since 4.0.0
     */
    public function onUserAfterDelete($user, $success, $msg): bool
    {
        if (!$success) {
            return false;
        }

        $query = $this->db->getQuery(true);
        $query->delete($this->db->quoteName('#__volunteers_volunteers'));
        $query->where($this->db->quoteName('user_id') . ' = ' . $this->db->quote($user['id']));
        $this->db->setQuery($query);

        return $this->db->execute();
    }

    /**
     *
     * Checks to see if user being deleted was or is a Joomla! volunteer
     *
     * @param array $user
     *
     * @return bool
     *
     * @since 4.0.0
     */
    public function onUserBeforeDelete($user): bool
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('members.id'))
            ->from($this->db->quoteName('#__volunteers_members', 'members'))
            ->leftJoin($this->db->quoteName('#__volunteers_volunteers') . ' AS volunteers ON ' . $this->db->quoteName('volunteers.id') . ' = ' . $this->db->quoteName('members.volunteer'))
            ->where($this->db->quoteName('volunteers.user_id') . ' = ' . $this->db->quote($user['id']));

        $volunteer = $this->db->setQuery($query)->loadResult();

        if ($volunteer) {
            $this->getApplication()->enqueueMessage(Text::sprintf('PLG_USER_VOLUNTEERS_CANNOT_DELETE', $user['name']), 'error');
            $this->getApplication()->redirect('index.php?option=com_users&view=users');
        }

        return true;
    }
}
