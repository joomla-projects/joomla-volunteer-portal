<?php

/**
 * @package    Com_Volunteers
 * @version    4.0.0
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2023 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\Controller;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Department controller class.
 *
 * @since  4.0.0
 */
class DepartmentController extends FormController
{
    protected $view_list = 'departments';

    /**
     * Method to edit department data
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
        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $this->app->getUserState('com_volunteers.edit.department.id');

        // Get variables
        $departmentId = $this->input->getInt('id');

        $acl = VolunteersHelper::acl('department', $departmentId);

        // Check if the user is authorized to edit this department
        if (!$acl->edit) {
            throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $departmentId), 403);
        }

        // Use parent edit method
        //return parent::edit($key, $urlVar);
        $this->setRedirect(Route::_('index.php?option=com_volunteers&view=departmentform&layout=edit', false));
    }

    /**
     * Method to save department data
     *
     * @param   null  $key
     * @param   null  $urlVar
     *
     * @return  boolean
     * @since 4.0.0
     * @throws Exception
     */
    public function save($key = null, $urlVar = null): bool
    {
        // Check for request forgeries.
        $this::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        // Get variables
        $departmentId = $this->input->getInt('id');
        $acl          = VolunteersHelper::acl('department', $departmentId);

        // Check if the user is authorized to edit this department
        if (!$acl->edit) {
            throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID'), $departmentId, 403);
        }

        // Use parent save method
        $return = parent::save($key, $urlVar);

        // Redirect to the department
        $this->setMessage(Text::_('COM_VOLUNTEERS_LBL_TEAM_SAVED'));
        $this->setRedirect(Route::_('index.php?option=com_volunteers&view=department&id=' . $departmentId, false));

        return $return;
    }

    /**
     * Method to cancel member data.
     *
     * @param   null  $key
     *
     * @return  boolean
     *
     * @since 4.0.0
     */
    public function cancel($key = null): bool
    {
        // Get variables
        $departmentId = $this->input->getInt('id');

        // Use parent save method
        $return = parent::cancel($key);

        $this->setRedirect(Route::_('index.php?option=com_volunteers&view=department&id=' . $departmentId, false));

        return $return;
    }

    /**
     * Method to send an email to department.
     *
     * @return  void
     * @since 4.0.0
     * @throws Exception
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendMail()
    {
        // Check for request forgeries.
        $this::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        // Get variables
        $session      = Factory::getApplication()->getSession();
        $user         = $session->get('user');
        $departmentId = $session->get('department');
        $subject      = $this->input->getString('subject', '');
        $message      = $this->input->getString('message', '');

        // Get department
        $department = $this->getModel()->getItem($departmentId);

        // Prefix the subject with the team name for easier identification where this comes from
        $subject = '[' . $department->title . '] ' . $subject;

        // Fallback for missing department email
        if (empty($department->email)) {
            // Get lead

            $lead = $this->getMVCFactory()->createModel('Members', 'Administrator', ['ignore_request' => true]);
            $lead->setState('filter.department', $departmentId);
            $lead->setState('list.limit', 1);
            $lead->setState('list.ordering', 'position');
            $lead->setState('list.direction', 'asc');
            $lead = $lead->getItems();

            $department->email = $lead[0]->user_email;
        }

        // Get a reference to the Joomla! mailer object
        $mailer = Factory::getMailer();

        // Set the sender
        $mailer->addReplyTo($user->email, $user->name);

        // Set the recipient
        $mailer->addRecipient($department->email, $department->title);

        // Set the subject
        $mailer->setSubject($subject);

        // Set the body
        $mailer->setBody($message);

        // Send the email
        $send = $mailer->Send();

        // Handle the message
        if ($send == true) {
            Factory::getApplication()->enqueueMessage(Text::_('COM_VOLUNTEERS_MESSAGE_SEND_SUCCESS'), 'message');
        } else {
            Factory::getApplication()->enqueueMessage(Text::_('JERROR_SENDING_EMAIL'), 'warning');
        }

        Factory::getApplication()->redirect(Route::_('index.php?option=com_volunteers&view=department&id=' . $departmentId, false));
    }
}
