<?php

/**
 * @package    Com_Volunteers
 * @version    4.0.0
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2023 The Joomla Project
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
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;
use stdClass;

/**
 * Member controller class.
 *
 * @since 4.0.0
 */
class MemberController extends FormController
{
    protected $view_list = 'members';

    /**
     * Method to add member
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
        $departmentId = $this->input->getInt('department');
        $teamId       = $this->input->getInt('team');
        $acl          = new stdClass();

        // Department or team?
        if ($departmentId) {
            $acl = VolunteersHelper::acl('department', $departmentId);
            Factory::getApplication()->setUserState('com_volunteers.edit.member.departmentid', $departmentId);
        } elseif ($teamId) {
            $acl = VolunteersHelper::acl('team', $teamId);
            Factory::getApplication()->setUserState('com_volunteers.edit.member.teamid', $teamId);
        }

        // Check if the user is authorized to edit this team
        if (!$acl->edit) {
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
     * @since 4.0.0
     * @throws Exception
     */
    public function cancel($key = null): bool
    {
        // Get variables
        $app          = Factory::getApplication();
        $departmentId = $app->getUserState('com_volunteers.edit.member.departmentid');
        $teamId       = $app->getUserState('com_volunteers.edit.member.teamid');

        // Use parent save method
        $return = parent::cancel($key);

        // Department or team?
        if ($departmentId) {
            Factory::getApplication()->setUserState('com_volunteers.edit.member.departmentid', null);
            $this->setRedirect(Route::_('index.php?option=com_volunteers&view=department&id=' . $departmentId, false));
        } elseif ($teamId) {
            Factory::getApplication()->setUserState('com_volunteers.edit.member.teamid', null);
            $this->setRedirect(Route::_('index.php?option=com_volunteers&view=team&id=' . $teamId, false));
        }

        return $return;
    }

    /**
     * Method to edit member data
     *
     * @param   null  $key
     * @param   null  $urlVar
     *
     * @return  boolean
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function edit($key = null, $urlVar = null): bool
    {
        // Get variables
        $memberId = $this->input->getInt('id');
        $member   = $this->getModel()->getItem($memberId);
        $acl      = new stdClass();

        // Department or team?
        if ($member->department) {
            $acl = VolunteersHelper::acl('department', $member->department);
            Factory::getApplication()->setUserState('com_volunteers.edit.member.departmentid', $member->department);
        } elseif ($member->team) {
            $acl = VolunteersHelper::acl('team', $member->team);
            Factory::getApplication()->setUserState('com_volunteers.edit.member.teamid', $member->team);
        }

        // Check if the user is authorized to edit this team
        if (!$acl->edit) {
            throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $memberId), 403);
        }

        // Use parent edit method
        return parent::edit($key, $urlVar);
    }

    /**
     * Method to save member data.
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
        $app          = Factory::getApplication();
        $memberId     = $this->input->getInt('id');
        $member       = $this->getModel()->getItem($memberId);
        $departmentId = ($memberId) ? $member->department : $app->getUserState('com_volunteers.edit.member.departmentid');
        $teamId       = ($memberId) ? $member->team : $app->getUserState('com_volunteers.edit.member.teamid');
        $acl          = new stdClass();
        // Department or team?
        if ($departmentId) {
            Factory::getApplication()->setUserState('com_volunteers.edit.member.departmentid', null);
            $acl = VolunteersHelper::acl('department', $departmentId);
        } elseif ($teamId) {
            Factory::getApplication()->setUserState('com_volunteers.edit.member.teamid', null);
            $acl = VolunteersHelper::acl('team', $teamId);
        }

        // Check if the user is authorized to edit this team
        if (!$acl->edit) {
            throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $memberId), 403);
        }

        // Use parent save method
        $return = parent::save($key, $urlVar);

        // Redirect to the team
        $this->setMessage(Text::_('COM_VOLUNTEERS_LBL_MEMBER_SAVED'));

        // Department or team?
        if ($departmentId) {
            $this->setRedirect(Route::_('index.php?option=com_volunteers&view=department&id=' . $departmentId, false));
        } elseif ($teamId) {
            $this->setRedirect(Route::_('index.php?option=com_volunteers&view=team&id=' . $teamId, false));
        }

        return $return;
    }
}
