<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/** @var $this \Joomla\Component\Volunteers\Site\View\Team\HtmlView */

// Import CSS and set up default tab
$tabneeded = "viewmembers";
try {
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->useStyle('com_volunteers.j3template')
        ->useStyle('com_volunteers.frontend');
    $tabn = Factory::getApplication()->getInput()->get('tab');
    if (!is_null($tabn)) {
        $tabneeded = "view" . $tabn;
    }
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}
?>
<div class="row-fluid">
    <div class="filter-bar">
        <?php if ($this->acl->edit) { ?>
            <a class="btn pull-right"
                href="<?php echo Route::_('index.php?option=com_volunteers&task=team.edit&id=' . $this->item->id) ?>">
                <span class="icon-edit" aria-hidden="true"></span>
                <?php echo Text::_('COM_VOLUNTEERS_TITLE_TEAMS_EDIT') ?>
            </a>
        <?php } ?>
    </div>
    <div class="page-header">
        <h1>
            <?php echo $this->escape($this->item->title) ?>
            <?php if ($this->item->acronym) { ?>
                (
                <?php echo ($this->item->acronym) ?>)
            <?php } ?>
            <?php if (!$this->item->active) { ?>
                <small>
                    <?php echo Text::_('COM_VOLUNTEERS_ARCHIVED') ?>
                </small>
            <?php } ?>
            <span class="label label-info">
                <?php if ($this->item->status == '0') {
                    echo Text::_('COM_VOLUNTEERS_FIELD_STATUS_INFORMATION');
                } elseif ($this->item->status == '1') {
                    echo Text::_('COM_VOLUNTEERS_FIELD_STATUS_OFFICIAL');
                } elseif ($this->item->status == '2') {
                    echo Text::_('COM_VOLUNTEERS_FIELD_STATUS_UNOFFICIAL');
                } ?>
            </span>
        </h1>
    </div>

    <div class="lead">
        <?php echo $this->item->description; ?>
    </div>

    <dl class="dl-horizontal">
        <?php if ($this->item->website) { ?>
            <dt>
                <?php echo Text::_('COM_VOLUNTEERS_FIELD_WEBSITE') ?>
            </dt>
            <dd><a href="<?php echo ($this->item->website) ?>"><?php echo ($this->item->website) ?></a></dd>
        <?php } ?>

        <?php if (($this->item->department_title) && ($this->item->department != 58)) { ?>
            <dt>
                <?php echo Text::_('COM_VOLUNTEERS_FIELD_DEPARTMENT') ?>
            </dt>
            <dd>
                <a
                    href="<?php echo Route::_('index.php?option=com_volunteers&view=department&id=' . $this->item->department); ?>"><?php echo $this->item->department_title; ?></a>
            </dd>
        <?php } ?>

        <?php if ($this->item->parent_id) { ?>
            <dt>
                <?php echo Text::_('COM_VOLUNTEERS_FIELD_TEAM_PARENT') ?>
            </dt>
            <dd>
                <a href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $this->item->parent_id); ?>"><?php echo $this->item->parent_title; ?></a>
            </dd>
        <?php } ?>

        <?php if ($this->item->date_started != '0000-00-00') { ?>
            <dt>
                <?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_STARTED') ?>
            </dt>
            <dd>
                <?php echo VolunteersHelper::date($this->item->date_started, 'F Y'); ?>
            </dd>
        <?php } ?>

        <?php if (!$this->item->active) { ?>
            <dt>
                <?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_ENDED') ?>
            </dt>
            <dd>
                <?php echo VolunteersHelper::date($this->item->date_ended, 'F Y'); ?>
            </dd>
        <?php } ?>
    </dl>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php
        echo HTMLHelper::_('uitab.startTabSet', 'teamsTab', array('active' => $tabneeded));

        /************************ TAB ***********************************/
        if ($this->item->active) {
            echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewmembers', Text::_('COM_VOLUNTEERS_TAB_MEMBERS'));
            echo $this->loadTemplate('members');
            echo HTMLHelper::_('uitab.endTab');
        }

        /************************ TAB ***********************************/
        if ($this->item->members->honorroll) {
            echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewhonourroll', Text::_('COM_VOLUNTEERS_TAB_HONORROLL'));
            echo $this->loadTemplate('honourroll');
            echo HTMLHelper::_('uitab.endTab');
        }

        /************************ TAB ***********************************/
        if (!$this->item->parent_id && ($this->item->subteams || $this->acl->create_team)) {
            echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewsubteams', Text::_('COM_VOLUNTEERS_TAB_SUBTEAMS'));
            echo $this->loadTemplate('subteams');
            echo HTMLHelper::_('uitab.endTab');
        }

        /************************ TAB ***********************************/
        if ($this->item->active) {
            echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewroles', Text::_('COM_VOLUNTEERS_TAB_ROLES'));
            echo $this->loadTemplate('roles');
            echo HTMLHelper::_('uitab.endTab');
        }

        /************************ TAB ***********************************/
        echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewreports', Text::_('COM_VOLUNTEERS_TAB_REPORTS'));
        echo $this->loadTemplate('reports');
        echo HTMLHelper::_('uitab.endTab');

        /************************ TAB ***********************************/
        if ($this->item->active) {
            echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewgetinvolved', Text::_('COM_VOLUNTEERS_TAB_GETINVOLVED'));

            if ($this->item->getinvolved) {
                echo $this->item->getinvolved;
            } else {
                echo '<a  class="btn" data-toggle="tab"  onclick="document.getElementById(\'teamsTab\').activateTab(document.getElementById(\'viewcontact\'));">' . Text::_('COM_VOLUNTEERS_USE_CONTACT') . '</a>';
            }

            echo HTMLHelper::_('uitab.endTab');

            /************************ TAB ***********************************/
            echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewcontact', Text::_('COM_VOLUNTEERS_TAB_CONTACT'));
            echo $this->loadTemplate('contact');
            echo HTMLHelper::_('uitab.endTab');
        }

        echo HTMLHelper::_('uitab.endTabSet');
    ?>
    </div>
</div>
