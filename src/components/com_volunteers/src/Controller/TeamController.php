<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\Controller;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\Component\Volunteers\Administrator\Model\MembersModel;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;
use RuntimeException;
use stdClass;

/**
 * Team controller class.
 *
 * @since 4.0.0
 */
class TeamController extends FormController
{
    protected $view_list = 'teams';

    /**
     * Method to add team
     *
     * @param   null  $key
     * @param   null  $urlVar
     *
     * @return  boolean
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function add($key = null, $urlVar = null): bool
    {
        // Get variables
        $department = $this->input->getInt('department');
        $team       = $this->input->getInt('team');
        $acl        = new stdClass();
        if ($department) {
            $departmentId = $department;
            $teamId       = null;
            $acl          = VolunteersHelper::acl('department', $departmentId);
        }

        if ($team) {
            $teamId       = $team;
            $departmentId = $this->getModel()->getItem($teamId)->department;
            $acl          = VolunteersHelper::acl('team', $teamId);
        }

        Factory::getApplication()->setUserState('com_volunteers.edit.team.departmentid', $departmentId);
        Factory::getApplication()->setUserState('com_volunteers.edit.team.teamid', $teamId);

        // Check if the user is authorized to edit this team
        if (!$acl->create_team) {
            throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $teamId), 403);
        }

        // Use parent add method
        return parent::add();
    }

    /**
     * Method to cancel member data.
     *
     * @param   null  $key
     *
     * @return  boolean
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function cancel($key = null): bool
    {
        // Get variables
        $app    = Factory::getApplication();
        $teamId = $this->input->getInt('id');
        $team   = $this->getModel()->getItem($teamId);
        $teamId = ($teamId) ? $team->id : $app->getUserState('com_volunteers.edit.team.teamid');

        // Use parent save method
        $return = parent::cancel($key);

        $this->setRedirect(Route::_('index.php?option=com_volunteers&view=team&id=' . $teamId, false));

        return $return;
    }

    /**
     * Method to edit team data
     *
     * @param   null  $key
     * @param   null  $urlVar
     *
     * @return  boolean
     * @since 4.0.0
     * @throws Exception
     */
    public function edit($key = null, $urlVar = null): bool
    {
        // Get variables
        $teamId = $this->input->getInt('id');
        $acl    = VolunteersHelper::acl('team', $teamId);

        // Check if the user is authorized to edit this team
        if (!$acl->edit) {
            throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $teamId), 403);
        }

        // Use parent edit method
        return parent::edit($key, $urlVar);
    }

    /**
     * Method to save team data
     *
     * @param   null  $key
     * @param   null  $urlVar
     *
     * @return  boolean
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function save($key = null, $urlVar = null): bool
    {
        // Check for request forgeries.
        $this::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        // Get variables
        $app    = Factory::getApplication();
        $teamId = $this->input->getInt('id');
        $team   = $this->getModel()->getItem($teamId);
        $teamId = ($teamId) ? $team->id : $app->getUserState('com_volunteers.edit.team.teamid');

        Factory::getApplication()->setUserState('com_volunteers.edit.member.teamid', null);
        $acl = VolunteersHelper::acl('team', $teamId);

        // Check if the user is authorized to edit this team
        if (!$acl->edit && $teamId) {
            throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $teamId), 403);
        }

        // Use parent save method
        $return = parent::save($key, $urlVar);

        // Redirect to the team
        $this->setMessage(Text::_('COM_VOLUNTEERS_LBL_TEAM_SAVED'));
        $this->setRedirect(Route::_('index.php?option=com_volunteers&view=team&id=' . $teamId, false));

        return $return;
    }

    /**
     * Method to send an email to team.
     *
     * @return  void
     * @since 4.0.0
     * @throws Exception
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendMail()
    {
        // Check for request forgeries.
        $this::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        // Get variables
        $app     = Factory::getApplication();
        $session = $app->getSession();
        $user    = $session->get('user');
        $teamId  = $session->get('team');
        $subject = $this->input->getString('subject', '');
        $message = $this->input->getString('message', '');

        // Get team
        $team = $this->getModel()->getItem($teamId);

        // Prefix the subject with the team name for easier identification where this comes from
        $subject = '[' . $team->title . '] ' . $subject;

        // Fallback for missing team email
        if (empty($team->email)) {
            // Get lead
            /** @var MembersModel $lead */
            $lead = $this->getMVCFactory()->createModel('Members', 'Administrator', ['ignore_request' => true]);
            $lead->setState('filter.team', $teamId);
            $lead->setState('filter.position', [2, 3, 5, 6]);
            $lead->setState('filter.active', 1);
            $lead->setState('list.limit', 1);
            $lead->setState('list.ordering', 'position');
            $lead->setState('list.direction', 'asc');
            $lead = $lead->getItems();

            $team->email = $lead[0]->user_email;
        }

        // Get email department coordinator for CC
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query
            ->select('user.email, user.name')
            ->from('#__volunteers_members as member')
            ->join('LEFT', $db->quoteName('#__volunteers_volunteers', 'volunteer') . ' ON ' . $db->qn('member.volunteer') . ' = ' . $db->qn('volunteer.id'))
            ->join('LEFT', $db->quoteName('#__users', 'user') . ' ON ' . $db->qn('volunteer.user_id') . ' = ' . $db->qn('user.id'))
            ->where($db->quoteName('member.department') . ' = ' . (int) $team->department)
            ->where($db->quoteName('member.position') . ' = ' . 11)
            ->where($db->quoteName('member.date_ended') . ' = ' . $db->quote('0000-00-00'));

        try {
            $coordinator = $db->setQuery($query)->loadObject();
        } catch (RuntimeException $e) {
            $app->enqueueMessage(Text::_('JERROR_SENDING_EMAIL'), 'warning');
        }

        // Get a reference to the Joomla! mailer object
        $mailer = Factory::getMailer();

        // Set the sender
        $mailer->addReplyTo($user->email, $user->name);

        // Set the recipient
        $mailer->addRecipient($team->email, $team->title);
        $mailer->addCc($coordinator->email, $coordinator->name);

        // Set the subject
        $mailer->setSubject($subject);

        // Set the body
        $mailer->setBody($message);

        // Send the email
        $send = $mailer->Send();

        // Handle the message
        if ($send == true) {
            $app->enqueueMessage(Text::_('COM_VOLUNTEERS_MESSAGE_SEND_SUCCESS'), 'message');
        } else {
            $app->enqueueMessage(Text::_('JERROR_SENDING_EMAIL'), 'warning');
        }

        $app->redirect(Route::_('index.php?option=com_volunteers&view=team&id=' . $teamId, false));
    }
}
