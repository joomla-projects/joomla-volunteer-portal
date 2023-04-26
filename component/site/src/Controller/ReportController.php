<?php

/**
 * @package    Com_Volunteers
 * @version    4.0.0
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2023 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\Controller;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use stdClass;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;

/**
 * Report controller class.
 *
 * @since 4.0.0
 */
class ReportController extends FormController
{
    /**
     * Method to add report
     *
     * @param   null  $key
     * @param   null  $urlVar
     *
     * @return  boolean
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
            Factory::getApplication()->setUserState('com_volunteers.edit.report.departmentid', $departmentId);
        } elseif ($teamId) {
            $acl = VolunteersHelper::acl('team', $teamId);
            Factory::getApplication()->setUserState('com_volunteers.edit.report.teamid', $teamId);
        }

        // Check if the user is authorized to edit this team
        if (!$acl->create_report) {
            throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $teamId), 403); //was $memberId in original but not defined
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
        $departmentId = $app->getUserState('com_volunteers.edit.report.departmentid');
        $teamId       = $app->getUserState('com_volunteers.edit.report.teamid');

        // Use parent save method
        $return = parent::cancel($key);

        // Department or team?
        if ($departmentId) {
            Factory::getApplication()->setUserState('com_volunteers.edit.report.departmentid', null);
            $this->setRedirect(Route::_('index.php?option=com_volunteers&view=department&id=' . $departmentId . '#reports', false));
        } elseif ($teamId) {
            Factory::getApplication()->setUserState('com_volunteers.edit.report.teamid', null);
            $this->setRedirect(Route::_('index.php?option=com_volunteers&view=team&id=' . $teamId . '#reports', false));
        }

        return $return;
    }

    /**
     * Method to edit report data
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
        $reportId = $this->input->getInt('id');
        $report   = $this->getModel()->getItem($reportId);
        $userId   = Factory::getApplication()->getSession()->get('user')->get('id');
        $acl      = new stdClass();

        // Department or team?
        if ($report->department) {
            $acl = VolunteersHelper::acl('department', $report->department);
            Factory::getApplication()->setUserState('com_volunteers.edit.report.departmentid', $report->department);
        } elseif ($report->team) {
            $acl = VolunteersHelper::acl('team', $report->team);
            Factory::getApplication()->setUserState('com_volunteers.edit.report.teamid', $report->team);
        }

        // Check if the user is authorized to edit this team
        if (!$acl->edit && ($userId != $report->created_by)) {
            throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $reportId), 403);
        }

        // Use parent edit method
        return parent::edit($key, $urlVar);
    }

    /**
     * Method to save report data.
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
        $app          = Factory::getApplication();
        $reportId     = ($this->input->getInt('id')) ? $this->input->getInt('id') : null;
        $report       = $this->getModel()->getItem($reportId);
        $departmentId = (int) ($reportId) ? $report->department : $app->getUserState('com_volunteers.edit.report.departmentid');
        $teamId       = (int) ($reportId) ? $report->team : $app->getUserState('com_volunteers.edit.report.teamid');
        $userId       = Factory::getApplication()->getSession()->get('user')->get('id');
        $acl          = new stdClass();
        // Department or team?
        if ($departmentId) {
            Factory::getApplication()->setUserState('com_volunteers.edit.report.departmentid', null);
            $acl = VolunteersHelper::acl('department', $departmentId);
        } elseif ($teamId) {
            Factory::getApplication()->setUserState('com_volunteers.edit.report.teamid', null);
            $acl = VolunteersHelper::acl('team', $teamId);
        }

        // Check if the user is authorized to edit this team
        if (!$acl->edit && !$acl->create_report && ($userId != $report->created_by)) {
            throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $userId), 403); // was memberId
        }

        // Use parent save method
        $return = parent::save($key, $urlVar);

        // Redirect to the team
        $this->setMessage(Text::_('COM_VOLUNTEERS_LBL_REPORT_SAVED'));

        // Department or team?
        if ($departmentId) {
            $this->setRedirect(Route::_('index.php?option=com_volunteers&view=department&id=' . $departmentId . '#reports', false));
        } elseif ($teamId) {
            $this->setRedirect(Route::_('index.php?option=com_volunteers&view=team&id=' . $teamId . '#reports', false));
        }

        return $return;
    }
}
