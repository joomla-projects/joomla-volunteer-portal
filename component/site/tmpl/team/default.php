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

defined('_JEXEC') or die;


// Import CSS and set up default tab
$tabneeded = "viewmembers";
try {
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->useStyle('com_volunteers.j3template')
        ->useStyle('com_volunteers.frontend');
    $tabn = Factory::getApplication()->input->get('tab');
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
                <span class="icon-edit"></span> <?php echo Text::_('COM_VOLUNTEERS_TITLE_TEAMS_EDIT') ?>
            </a>
        <?php } ?>
    </div>
    <div class="page-header">
        <h1>
            <?php echo $this->escape($this->item->title) ?>
            <?php if ($this->item->acronym) { ?>
                (<?php echo($this->item->acronym) ?>)
            <?php } ?>
            <?php if (!$this->item->active) { ?>
                <small><?php echo Text::_('COM_VOLUNTEERS_ARCHIVED') ?></small>
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

    <div class="lead"><?php echo $this->item->description; ?></div>

    <dl class="dl-horizontal">
        <?php if ($this->item->website) { ?>
            <dt><?php echo Text::_('COM_VOLUNTEERS_FIELD_WEBSITE') ?></dt>
            <dd><a href="<?php echo($this->item->website) ?>"><?php echo($this->item->website) ?></a></dd>
        <?php } ?>

        <?php if (($this->item->department_title) && ($this->item->department != 58)) { ?>
            <dt><?php echo Text::_('COM_VOLUNTEERS_FIELD_DEPARTMENT') ?></dt>
            <dd>
                <a href="<?php echo Route::_('index.php?option=com_volunteers&view=department&id=' . $this->item->department); ?>"><?php echo $this->item->department_title; ?></a>
            </dd>
        <?php } ?>

        <?php if ($this->item->parent_id) { ?>
            <dt><?php echo Text::_('COM_VOLUNTEERS_FIELD_TEAM_PARENT') ?></dt>
            <dd>
                <a href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $this->item->parent_id); ?>"><?php echo $this->item->parent_title; ?></a>
            </dd>
        <?php } ?>

        <?php if ($this->item->date_started != '0000-00-00') { ?>
            <dt><?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_STARTED') ?></dt>
            <dd><?php echo VolunteersHelper::date($this->item->date_started, 'F Y'); ?></dd>
        <?php } ?>

        <?php if (!$this->item->active) { ?>
            <dt><?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_ENDED') ?></dt>
            <dd><?php echo VolunteersHelper::date($this->item->date_ended, 'F Y'); ?></dd>
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
        }
        ?>
        <?php if ($this->acl->edit) { ?>
        <div class="row-fluid">
            <a class="btn pull-right"
               href="<?php echo Route::_('index.php?option=com_volunteers&task=member.add&team=' . $this->item->id) ?>">
                <span class="icon-new"></span> <?php echo Text::_('COM_VOLUNTEERS_MEMBER_ADD') ?>
            </a>
        </div>
        <hr>
            <?php
        } ?>
        <?php if ($this->item->members->active) { ?>
        <table class="table table-striped table-hover table-vertical-align">
            <thead>
            <th width="30%"><?php echo Text::_('COM_VOLUNTEERS_FIELD_VOLUNTEER') ?></th>
            <th width="20%"><?php echo Text::_('COM_VOLUNTEERS_FIELD_POSITION') ?></th>
            <th><?php echo Text::_('COM_VOLUNTEERS_FIELD_ROLE') ?></th>
            <th width="12%"><?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_STARTED') ?></th>
            <?php if ($this->acl->edit) { ?>
                <th width="10%" class="center"><?php echo Text::_('COM_VOLUNTEERS_FIELD_ADDRESS') ?></th>
                <th width="10%" class="center"><?php echo Text::_('COM_VOLUNTEERS_FIELD_NDA') ?></th>
                <th width="10%"></th>
                <?php
            } ?>
            </thead>
            <tbody>
            <?php foreach ($this->item->members->active as $volunteer) { ?>
                <tr>
                    <td class="volunteer-image">
                      <?php VolunteersHelper::OutputVolunteer($volunteer);  ?>
                    </td>
                    <td>
                        <?php echo $volunteer->position_title; ?>
                    </td>
                    <td>
                        <?php echo $volunteer->role_title; ?>
                    </td>
                    <td>
                        <?php echo VolunteersHelper::date($volunteer->date_started, 'M Y'); ?>
                    </td>
                    <?php if ($this->acl->edit) { ?>
                        <td class="center">
                            <?php
                            if ($volunteer->address) { ?>
                                <span class="icon-checkbox-checked"></span>
                            <?php } else { ?>
                                <span class="icon-checkbox-unchecked"></span>
                            <?php } ?>
                        </td>
                        <td class="center">
                            <?php if ($volunteer->nda) { ?>
                                <span class="icon-checkbox-checked"></span>
                            <?php } else { ?>
                                <span class="icon-checkbox-unchecked"></span>
                            <?php } ?>
                        </td>
                        <td>
                            <a class="btn btn-small pull-right"
                               href="<?php echo Route::_('index.php?option=com_volunteers&task=member.edit&id=' . $volunteer->id) ?>">
                                <span class="icon-edit"></span> <?php echo Text::_('COM_VOLUNTEERS_EDIT') ?>
                            </a>
                        </td>
                        <?php
                    } ?>
                </tr>
                <?php
            } ?>
            </tbody>
        </table>
            <?php
            echo HTMLHelper::_('uitab.endTab');
        } ?>

        <?php
        /************************ TAB ***********************************/
        if ($this->item->members->honorroll) {
            echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewhonourroll', Text::_('COM_VOLUNTEERS_TAB_HONORROLL'));
            if ($this->acl->edit) { ?>
        <div class="row-fluid">
            <a class="btn pull-right"
               href="<?php echo Route::_('index.php?option=com_volunteers&task=member.add&team=' . $this->item->id) ?>">
                <span class="icon-new"></span> <?php echo Text::_('COM_VOLUNTEERS_MEMBER_ADD') ?>
            </a>
        </div>
        <hr>
            <?php } ?>
        <table class="table table-striped table-hover table-vertical-align">
            <thead>
            <th width="30%"><?php echo Text::_('COM_VOLUNTEERS_FIELD_VOLUNTEER') ?></th>
            <th width="20%"><?php echo Text::_('COM_VOLUNTEERS_FIELD_POSITION') ?></th>
            <th><?php echo Text::_('COM_VOLUNTEERS_FIELD_ROLE') ?></th>
            <th width="12%"><?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_STARTED') ?></th>
            <th width="12%"><?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_ENDED') ?></th>
            <?php if ($this->acl->edit) { ?>
                <th width="10%"></th>
            <?php } ?>
            </thead>
            <tbody>
            <?php foreach ($this->item->members->honorroll as $volunteer) { ?>
                <tr>
                    <td class="volunteer-image">
                       <?php VolunteersHelper::OutputVolunteer($volunteer); ?>
                    </td>
                    <td>
                        <?php echo $volunteer->position_title; ?>
                    </td>
                    <td>
                        <?php echo $volunteer->role_title; ?>
                    </td>
                    <td>
                        <?php echo VolunteersHelper::date($volunteer->date_started, 'M Y'); ?>
                    </td>
                    <td>
                        <?php echo VolunteersHelper::date($volunteer->date_ended, 'M Y'); ?>
                    </td>
                    <?php if ($this->acl->edit) { ?>
                        <td>
                            <a class="btn btn-small pull-right"
                               href="<?php echo Route::_('index.php?option=com_volunteers&task=member.edit&id=' . $volunteer->id) ?>">
                                <span class="icon-edit"></span> <?php echo Text::_('COM_VOLUNTEERS_EDIT') ?>
                            </a>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
            <?php
            echo HTMLHelper::_('uitab.endTab');
        }

        /************************ TAB ***********************************/
        if (!$this->item->parent_id && ($this->item->subteams || $this->acl->create_team)) {
            echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewsubteams', Text::_('COM_VOLUNTEERS_TAB_SUBTEAMS'));
            if ($this->acl->create_team) {
                ?>
        <div class="row-fluid">
            <a class="btn pull-right"
               href="<?php echo Route::_('index.php?option=com_volunteers&task=team.add&team=' . $this->item->id) ?>">
                <span class="icon-new"></span> <?php echo Text::_('COM_VOLUNTEERS_SUBTEAM_ADD') ?>
            </a>
        </div>
        <hr>
                <?php
            } ?>
            <?php foreach ($this->item->subteams as $i => $item) { ?>
        <div class="row-fluid">
            <div class="team well team-<?php echo($item->id); ?>">
                <div class="row-fluid">
                    <div class="span8">
                        <h2 style="margin-top: 0;">
                            <a href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $item->id) ?>">
                                <?php echo($item->title); ?><?php if ($item->acronym) {
                                    ?> (<?php echo($item->acronym) ?>)<?php
                                } ?>
                            </a>
                            <?php if ($item->date_ended != '0000-00-00') { ?>
                                <small><?php echo Text::_('COM_VOLUNTEERS_ARCHIVED') ?></small>
                            <?php } ?>
                        </h2>
                        <p><?php echo($item->description); ?></p>
                        <a href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $item->id) ?>"
                           class="btn">
                            <span class="icon-chevron-right"></span><?php echo Text::_('COM_VOLUNTEERS_READ_MORE') . ' ' . $item->title; ?>
                        </a>
                    </div>
                    <div class="span4">
                        <div class="members">
                            <?php $i = 0;
                            if (!empty($item->members)) {
                                foreach ($item->members as $member) { ?>
                                <a class="tip hasTooltip" title="<?php echo $member->volunteer_name; ?>"
                                   href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $member->volunteer) ?>">
                                    <?php echo VolunteersHelper::image($member->volunteer_image, 'small', false, $member->volunteer_image); ?>
                                </a>
                                    <?php $i++;
                                    if ($i == 14) {
                                        break;
                                    }
                                }
                            } ?>
                            if (count($item->members) > 14)
                            {
                            ?>
                            <a href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $item->id) ?>"
                               class="all-members">
                                <span class="all"><?php echo Text::_('COM_VOLUNTEERS_ALL') ?></span><span
                                        class="number"><?php echo(' ' . count($item->members)); ?></span>
                            </a>
                            <?php
            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <?php
            echo HTMLHelper::_('uitab.endTab');
        }

        /************************ TAB ***********************************/


        if ($this->item->active) {
            echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewroles', Text::_('COM_VOLUNTEERS_TAB_ROLES'));
            if ($this->acl->edit) {
                echo '<div class="row-fluid">';
                echo '<a class="btn pull-right"';
                echo 'href="' . Route::_('index.php?option=com_volunteers&task=role.add&team=' . $this->item->id) . '">';
                echo '<span class="icon-new"></span> ' . Text::_('COM_VOLUNTEERS_ROLE_ADD') . '</a>';
                echo '</div>';
                echo '<hr>';
            }
            if ($this->item->roles) {
                foreach ($this->item->roles as $role) {
                    echo '<div class="row-fluid">';
                    echo '<div class="team well">';
                    echo '<div class="row-fluid">';
                    echo '<div class="span8">';
                    if ($this->acl->edit) {
                        echo '<a class="btn btn-small pull-right"';
                        echo 'href="' . Route::_('index.php?option=com_volunteers&task=role.delete&id=' . $role->id) . '">';
                        echo '<span class="icon-delete"></span> ' . Text::_('COM_VOLUNTEERS_DELETE') . '</a>';

                        echo '<a class="btn btn-small pull-right"';
                        echo 'href="' . Route::_('index.php?option=com_volunteers&task=role.edit&id=' . $role->id) . '">';

                        echo '<span class="icon-delete"></span> ' . Text::_('COM_VOLUNTEERS_EDIT') . '</a>';
                    }
                    echo('<h2>' . $role->title . '</h2>');
                    echo('<p>' . $role->description . '</p>');
                    if ($role->open) {
                        echo '<a class="btn" data-toggle="tab" onclick="document.getElementById(\'teamsTab\').activateTab(document.getElementById(\'viewcontact\'));">';
                        echo '<span class="icon-chevron-right"></span>' . Text::_('COM_VOLUNTEERS_ROLE_APPLY') . '</a>';
                    }

                    echo '</div>';
                    echo '<div class="span4">';
                    echo '<div class="members">';
                    if (!empty($role->volunteers)) {
                        foreach ($role->volunteers as $rolevolunteer) {
                            echo '<a class="tip hasTooltip" title="' . $rolevolunteer->volunteer_name . '" ' .
                                ' href="' . Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $rolevolunteer->volunteer) . '">' .
                                VolunteersHelper::image($rolevolunteer->volunteer_image, 'small', false, $rolevolunteer->volunteer_image) . '</a>';
                        }
                    }
                    if ($role->open) {
                        echo '<a data-toggle="tab" class="all-members"  onclick="document.getElementById(\'teamsTab\').activateTab(document.getElementById(\'viewgetinvolved\'));">';
                        echo '<span class="all">' . Text::_('COM_VOLUNTEERS_YOU') . '</span><span class="number">?</span></a>';
                    }
                    echo '</div>';
                    echo '</div>'; // end span4
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                } //foreach
            } else {
                echo '<div class="row-fluid">';
                echo '<p class="alert alert-info">';
                echo Text::_('COM_VOLUNTEERS_NOTE_NO_ROLES');
                echo '</p>';
                echo '</div>';
            }

            echo HTMLHelper::_('uitab.endTab');
        }

        /************************ TAB ***********************************/
        echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewreports', Text::_('COM_VOLUNTEERS_TAB_REPORTS'));
        if ($this->acl->create_report) : ?>
        <div class="row-fluid">
            <a class="btn pull-right"
               href="<?php echo Route::_('index.php?option=com_volunteers&task=report.add&team=' . $this->item->id) ?>">
                <span class="icon-new"></span> <?php echo Text::_('COM_VOLUNTEERS_REPORT_ADD') ?>
            </a>
        </div>
        <hr>
        <?php endif; ?>
        <?php if ($this->item->reports) : ?>
            <?php foreach ($this->item->reports as $report) : ?>
        <div class="row-fluid report">
            <div class="span2 volunteer-image">
                <a href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $report->volunteer_id) ?>">
                    <?php echo VolunteersHelper::image($report->volunteer_image, 'large', false, $report->volunteer_name); ?>
                </a>
            </div>
            <div class="span10">
                <?php if ($this->acl->edit || ($this->user->id == $report->created_by)) : ?>
                    <a class="btn btn-small pull-right"
                       href="<?php echo Route::_('index.php?option=com_volunteers&task=report.edit&id=' . $report->id) ?>">
                        <span class="icon-edit"></span> <?php echo Text::_('COM_VOLUNTEERS_EDIT') ?>
                    </a>
                <?php endif; ?>
                <h2 class="report-title">
                    <a href="<?php echo Route::_('index.php?option=com_volunteers&view=report&id=' . $report->id) ?>">
                        <?php echo $report->title; ?>
                    </a>
                </h2>
                <p class="muted">
                    <?php echo Text::_('COM_VOLUNTEERS_BY') ?>
                    <a href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $report->volunteer_id) ?>"><?php echo $report->volunteer_name; ?></a>
                    <?php echo Text::_('COM_VOLUNTEERS_ON') ?> <?php echo VolunteersHelper::date($report->created, 'Y-m-d H:i'); ?>
                    <?php echo Text::_('COM_VOLUNTEERS_IN') ?>
                    <a href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $report->team) ?>"><?php echo $report->team_title; ?></a>
                </p>
                <p><?php echo HtmlHelper::_('string.truncate', strip_tags(trim($report->description)), 300); ?></p>
                <a href="<?php echo Route::_('index.php?option=com_volunteers&view=report&id=' . $report->id) ?>"
                   class="btn">
                    <?php echo Text::_('COM_VOLUNTEERS_READ_MORE') ?>&nbsp;<?php echo($report->title); ?>
                </a>
            </div>
        </div>
        <hr>
            <?php endforeach; ?>
            <?php if (count($this->item->reports) == 10) : ?>
        <a href="<?php echo Route::_('index.php?option=com_volunteers&view=reports') ?>?filter_category=t.<?php echo $this->item->id; ?>"
           class="btn">
            <span class="icon-chevron-right"></span><?php echo Text::_('COM_VOLUNTEERS_REPORTS_BROWSE') ?>&nbsp
        </a>
            <?php endif; ?>
        <a class="btn btn-warning pull-right"
           href="<?php echo Route::_('index.php?option=com_volunteers&view=reports&filter_category=t.' . $this->item->id . '&format=feed&type=rss') ?>">
            <span class="icon-feed"></span> <?php echo Text::_('COM_VOLUNTEERS_RSSFEED') ?>
        </a>
        <?php else : ?>
        <div class="row-fluid">
            <p class="alert alert-info">
                <?php echo Text::_('COM_VOLUNTEERS_NOTE_NO_REPORTS') ?>
            </p>
        </div>
        <?php endif;
        echo HTMLHelper::_('uitab.endTab');
        if ($this->item->active) {
            /************************ TAB ***********************************/

            echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewgetinvolved', Text::_('COM_VOLUNTEERS_TAB_GETINVOLVED'));
            if ($this->item->getinvolved) {
                echo $this->item->getinvolved;
            } else {
                echo '<a  class="btn" data-toggle="tab"  onclick="document.getElementById(\'teamsTab\').activateTab(document.getElementById(\'viewcontact\'));">' . Text::_('COM_VOLUNTEERS_USE_CONTACT') . '</a>';
            }

            echo HTMLHelper::_('uitab.endTab');
            /************************ TAB ***********************************/
            echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewcontact', Text::_('COM_VOLUNTEERS_TAB_CONTACT'));
            if ($this->user->guest) {
                echo '<p class="alert alert-info">';
                echo Text::_('COM_VOLUNTEERS_NOTE_LOGIN_CONTACT_TEAM') . '</p>';
            } else { ?>
            <form class="form form-horizontal" name="sendmail" action="<?php echo Route::_('index.php') ?>"
              method="post" enctype="multipart/form-data">
            <div class="control-group">
                <label class="control-label"
                       for="to_name"><?php echo Text::_('COM_VOLUNTEERS_MESSAGE_TO') ?></label>
                <div class="controls">
                    <input type="text" name="to_name" id="to_name"
                           value="<?php echo $this->escape($this->item->title); ?>" class="input-block-level"
                           disabled="disabled"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"
                       for="from_name"><?php echo Text::_('COM_VOLUNTEERS_MESSAGE_FROM') ?></label>
                <div class="controls">
                    <input type="text" name="from_name" id="from_name"
                           value="<?php echo $this->escape($this->user->name); ?> <<?php echo $this->escape($this->user->email); ?>>"
                           class="input-block-level" disabled="disabled"/>
                </div>
            </div>
            <div class="control-group">
                <div class="controls span12">
                    <input type="text" name="subject" id="subject" class="input-block-level"
                           placeholder="<?php echo Text::_('COM_VOLUNTEERS_MESSAGE_SUBJECT') ?>" required/>
                </div>
            </div>
            <div class="control-group">
                    <textarea rows="10" name="message" id="message" class="input-block-level"
                              placeholder="<?php echo Text::sprintf('COM_VOLUNTEERS_MESSAGE_BODY', $this->escape($this->item->title)) ?>"
                              required></textarea>
            </div>
            <div class="alert alert-info">
                <?php echo Text::sprintf('COM_VOLUNTEERS_MESSAGE_NOTICE', $this->escape($this->item->title)) ?>
            </div>
            <div class="control-group">
                <input type="submit" value="<?php echo Text::_('COM_VOLUNTEERS_MESSAGE_SUBMIT') ?>" name="submit"
                       id="submitButton" class="btn btn-success pull-right"/>
            </div>

            <input type="hidden" name="option" value="com_volunteers"/>
            <input type="hidden" name="task" value="team.sendmail"/>
                <?php echo HtmlHelper::_('form.token'); ?>
        </form>
                <?php
            }?>



            <?php
            echo HTMLHelper::_('uitab.endTab');
        }

        ?>