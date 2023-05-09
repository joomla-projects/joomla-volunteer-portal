<?php

/**
 * @version    CVS: 4.0.0
 * @package    Com_Volunteers
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2023 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\Service;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;

/**
 * Class VolunteersRouter
 *
 * @since 4.0.0
 */
class Router extends RouterView
{
    /**
     * @param   SiteApplication           $app
     * @param   AbstractMenu              $menu
     * @param   CategoryFactoryInterface  $categoryFactory
     * @param   DatabaseInterface         $db
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function __construct($app, $menu, CategoryFactoryInterface $categoryFactory, $db)
    {
        $departments = new RouterViewConfiguration('departments');
        $this->registerView($departments);
        $ccDepartment = new RouterViewConfiguration('department');
        $ccDepartment->setKey('id')->setParent($departments);
        $this->registerView($ccDepartment);
        $ccMember = new RouterViewConfiguration('member');
        $ccMember->setKey('id');
        $this->registerView($ccMember);
        $reports = new RouterViewConfiguration('reports');
        $this->registerView($reports);
        $ccReport = new RouterViewConfiguration('report');
        $ccReport->setKey('id')->setParent($reports);
        $this->registerView($ccReport);
        $roles = new RouterViewConfiguration('roles');
        $this->registerView($roles);
        $ccRole = new RouterViewConfiguration('role');
        $ccRole->setKey('id')->setParent($roles);
        $this->registerView($ccRole);
        $teams = new RouterViewConfiguration('teams');
        $this->registerView($teams);
        $ccTeam = new RouterViewConfiguration('team');
        $ccTeam->setKey('id')->setParent($teams);
        $this->registerView($ccTeam);
        $volunteers = new RouterViewConfiguration('volunteers');
        $this->registerView($volunteers);
        $ccVolunteer = new RouterViewConfiguration('volunteer');
        $ccVolunteer->setKey('id')->setParent($volunteers);
        $this->registerView($ccVolunteer);
        $board = new RouterViewConfiguration('board');
        $this->registerView($board);
        $home = new RouterViewConfiguration('home');
        $this->registerView($home);
        $my = new RouterViewConfiguration('my');
        $this->registerView($my);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }



    /**
     * Method to get the segment(s) for an department
     *
     * @param   string  $id     ID of the department to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array  The segments of this item
     *
     * @since 4.0.0
     */
    public function getDepartmentSegment(string $id, array $query): array
    {
        return [(int) $id => $id];
    }
    /**
     * Method to get the segment(s) for an member
     *
     * @param   string  $id     ID of the member to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array  The segments of this item
     *
     * @since 4.0.0
     */
    public function getMemberSegment(string $id, array $query): array
    {
        return [(int) $id => $id];
    }
    /**
     * Method to get the segment(s) for an report
     *
     * @param   string  $id     ID of the report to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array  The segments of this item
     *
     * @since 4.0.0
     */
    public function getReportSegment(string $id, array $query): array
    {
        return [(int) $id => $id];
    }
    /**
     * Method to get the segment(s) for an role
     *
     * @param   string  $id     ID of the role to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array  The segments of this item
     *
     * @since 4.0.0
     */
    public function getRoleSegment(string $id, array $query): array
    {
        return [(int) $id => $id];
    }
    /**
     * Method to get the segment(s) for an team
     *
     * @param   string  $id     ID of the team to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array  The segments of this item
     *
     * @since 4.0.0
     */
    public function getTeamSegment(string $id, array $query): array
    {
        return [(int) $id => $id];
    }
    /**
     * Method to get the segment(s) for an volunteer
     *
     * @param   string  $id     ID of the volunteer to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array  The segments of this item
     *
     * @since 4.0.0
     */
    public function getVolunteerSegment(string $id, array $query): array
    {
        return [(int) $id => $id];
    }


    /**
     * Method to get the segment(s) for an department
     *
     * @param   string  $segment  Segment of the department to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  int   The id of this item or false
     *
     * @since 4.0.0
     */
    public function getDepartmentId(string $segment, array $query): int
    {
        return (int) $segment;
    }
    /**
     * Method to get the segment(s) for an member
     *
     * @param   string  $segment  Segment of the member to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  int   The id of this item or false
     *
     * @since 4.0.0
     */
    public function getMemberId(string $segment, array $query): int
    {
        return (int) $segment;
    }
    /**
     * Method to get the segment(s) for an report
     *
     * @param   string  $segment  Segment of the report to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  int   The id of this item or false
     * @since 4.0.0
     */
    public function getReportId(string $segment, array $query): int
    {
        return (int) $segment;
    }
    /**
     * Method to get the segment(s) for an role
     *
     * @param   string  $segment  Segment of the role to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  int   The id of this item or false
     *
     * @since 4.0.0
     */
    public function getRoleId(string $segment, array $query): int
    {
        return (int) $segment;
    }
    /**
     * Method to get the segment(s) for an team
     *
     * @param   string  $segment  Segment of the team to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  int   The id of this item or false
     *
     * @since 4.0.0
     */
    public function getTeamId(string $segment, array $query): int
    {
        return (int) $segment;
    }
    /**
     * Method to get the segment(s) for an volunteer
     *
     * @param   string  $segment  Segment of the volunteer to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  int   The id of this item or false
     *
     * @since 4.0.0
     */
    public function getVolunteerId(string $segment, array $query): int
    {
        return (int) $segment;
    }
}
