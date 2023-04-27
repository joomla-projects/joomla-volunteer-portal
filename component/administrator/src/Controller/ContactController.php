<?php

/**
 * @version    4.0.0
 * @package    Com_Volunteers
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2023 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\Controller;

use Exception;
use ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Contact controller class.
 *
 * @since 4.0.0
 */
class ContactController extends FormController
{
    protected $view_list = 'contacts';
    /**
     * Send an email to all active volunteers
     *
     * @return  void
     * @since 4.0.0
     * @throws  Exception
     */
    public function send()
    {

        $mailer  = Factory::getMailer();
        $app     = Factory::getApplication();
        $session = Factory::getApplication()->getSession();
        $user    = $session->get('user');
        $canDo   = ContentHelper::getActions('com_volunteers');

        // Super users access only
        if (!$canDo->get('core.manage')) {
            throw new Exception('No access to mail sending', 403);
        }

        $mailData   = $app->input->get('jform', array(), 'Array');
        //$from       = $app->get('mailfrom');
        //$fromName   = $app->get('fromname');
        $subject    = trim($mailData['subject']);
        $body       = trim($mailData['message']);
        $recipients = $session->get('volunteers.recipients');

        // Collect the emails for the recipients
        $emails = [];

        foreach ($recipients as $recipient) {
            if ($recipient['email'] !== $user->email) {
                $emails[] = $recipient['email'];
            }
        }

        $emails = array_unique($emails);

        // Send mail
        $success = $mailer->sendMail(
            $app->get('mailfrom'),
            $app->get('fromname'),
            $user->email,
            $subject,
            $body,
            true,
            null,
            $emails,
            null,
            $user->email,
            $user->name
        );

        if (!$success) {
            $app->enqueueMessage(Text::_('COM_VOLUNTEERS_MESSAGE_SENDING_FAILED'), 'warning');
        }

        // Clear recipients
        $session->clear('volunteers.recipients');

        $this->setRedirect('index.php?option=com_volunteers&view=members', Text::_('COM_VOLUNTEERS_MESSAGE_SEND_SUCCESS'));
    }

    /**
     * Method for closing the contact form.
     *
     * @return  void
     * @since 1.0.0
     */
    public function cancel($key = null)
    {
        $this->setRedirect(Route::_('index.php?option=com_volunteers&view=members', false));
    }
}
