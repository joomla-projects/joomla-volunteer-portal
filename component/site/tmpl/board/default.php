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


// Import CSS
try {
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->useStyle('com_volunteers.j3template')
        ->useStyle('com_volunteers.frontend');
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}
?>
<div class="row-fluid">
    <div class="filter-bar">
        <?php if ($this->acl->edit) : ?>
            <a class="btn pull-right"
               href="<?php echo Route::_('index.php?option=com_volunteers&task=department.edit&id=' . $this->item->id) ?>"><br/>

                <span class="icon-edit"></span> <?php echo Text::_('COM_VOLUNTEERS_TITLE_DEPARTMENTS_EDIT') ?>
            </a>
        <?php endif; ?>
    </div>
    <div class="page-header">
        <h1>
            <?php echo $this->escape($this->item->title) ?>
        </h1>
    </div>

    <p class="lead"><?php echo strip_tags($this->item->description) ?></p>

    <dl class="dl-horizontal">
        <?php if ($this->item->website) : ?>
            <dt><?php echo Text::_('COM_VOLUNTEERS_FIELD_WEBSITE') ?></dt>
            <dd><a href="<?php echo($this->item->website) ?>"><?php echo($this->item->website) ?></a></dd>
        <?php endif; ?>
    </dl>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php
        echo HTMLHelper::_('uitab.startTabSet', 'departmentTab', array('active' => 'viewmembers'));
        echo HTMLHelper::_('uitab.addTab', 'departmentTab', 'viewmembers', Text::_('COM_VOLUNTEERS_TAB_MEMBERS'));
        ?>
        <?php if ($this->acl->edit) : ?>
            <div class="row-fluid">
                <a class="btn pull-right"
                   href="<?php echo Route::_('index.php?option=com_volunteers&task=member.add&department=' . $this->item->id) ?>">
                    <span class="icon-new"></span> <?php echo Text::_('COM_VOLUNTEERS_MEMBER_ADD') ?>
                </a>
            </div>
            <hr>
        <?php endif; ?>
        <?php if ($this->item->members->active) : ?>
            <table class="table table-striped table-hover table-vertical-align">
                <thead>
                <th width="30%"><?php echo Text::_('COM_VOLUNTEERS_FIELD_VOLUNTEER') ?></th>
                <th><?php echo Text::_('COM_VOLUNTEERS_FIELD_POSITION') ?></th>
                <th width="12%"><?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_STARTED') ?></th>
                <?php if ($this->acl->edit) : ?>
                    <th width="10%"><?php echo Text::_('COM_VOLUNTEERS_TITLE_MEMBERS_EDIT') ?></th>
                <?php endif; ?>
                </thead>
                <tbody>
                <?php foreach ($this->item->members->active as $volunteer) : ?>
                    <tr>
                        <td class="volunteer-image">
                            <?php VolunteersHelper::OutputVolunteer($volunteer); ?>
                        </td>
                        <td>
                            <?php if ($volunteer->position == 11) : ?>
                                <?php echo $volunteer->position_title; ?>:
                                <a href="<?php echo Route::_('index.php?option=com_volunteers&view=department&id=' . $volunteer->department) ?>">
                                    <?php echo $volunteer->department_title; ?>
                                </a>
                            <?php else : ?>
                                <?php echo $volunteer->role_title; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo VolunteersHelper::date($volunteer->date_started, 'M Y'); ?>
                        </td>
                        <?php if ($this->acl->edit) : ?>
                            <td>
                                <a class="btn btn-small pull-right"
                                   href="<?php echo Route::_('index.php?option=com_volunteers&task=member.edit&id=' . $volunteer->id) ?>">
                                    <span class="icon-edit"></span> <?php echo Text::_('COM_VOLUNTEERS_EDIT') ?>
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif;
        echo HTMLHelper::_('uitab.endTab');

        if ($this->item->members->honorroll) {
            echo HTMLHelper::_('uitab.addTab', 'departmentTab', 'viewhonourroll', Text::_('COM_VOLUNTEERS_TAB_HONORROLL'));
            ?>
            <?php if ($this->acl->edit) : ?>
            <div class="row-fluid">
                <a class="btn pull-right"
                   href="<?php echo Route::_('index.php?option=com_volunteers&task=member.add&department=' . $this->item->id) ?>">
                    <span class="icon-new"></span> <?php echo Text::_('COM_VOLUNTEERS_MEMBER_ADD') ?>
                </a>
            </div>
            <hr>
            <?php endif; ?>
            <table class="table table-striped table-hover table-vertical-align">
                <thead>
                <th width="30%"><?php echo Text::_('COM_VOLUNTEERS_FIELD_VOLUNTEER') ?></th>
                <th><?php echo Text::_('COM_VOLUNTEERS_FIELD_POSITION') ?></th>
                <th width="12%"><?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_STARTED') ?></th>
                <th width="12%"><?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_ENDED') ?></th>
                <?php if ($this->acl->edit) : ?>
                    <th width="10%"><?php echo Text::_('COM_VOLUNTEERS_TITLE_MEMBERS_EDIT') ?></th>
                <?php endif; ?>
                </thead>
                <tbody>
                <?php foreach ($this->item->members->honorroll as $volunteer) : ?>
                    <tr>
                        <td class="volunteer-image">
                            <?php VolunteersHelper::OutputVolunteer($volunteer); ?>
                        </td>
                        <td>
                            <?php if ($volunteer->position == 11) : ?>
                                <?php echo $volunteer->position_title; ?>:
                                <a href="<?php echo Route::_('index.php?option=com_volunteers&view=department&id=' . $volunteer->department) ?>">
                                    <?php echo $volunteer->department_title; ?>
                                </a>
                            <?php else : ?>
                                <?php echo $volunteer->role_title; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo VolunteersHelper::date($volunteer->date_started, 'M Y'); ?>
                        </td>
                        <td>
                            <?php echo VolunteersHelper::date($volunteer->date_ended, 'M Y'); ?>
                        </td>
                        <?php if ($this->acl->edit) : ?>
                            <td>
                                <a class="btn btn-small pull-right"
                                   href="<?php echo Route::_('index.php?option=com_volunteers&task=member.edit&id=' . $volunteer->id) ?>">
                                    <span class="icon-edit"></span> <?php echo Text::_('COM_VOLUNTEERS_EDIT') ?>
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>


            <?php echo HTMLHelper::_('uitab.endTab');
        }

        echo HTMLHelper::_('uitab.addTab', 'departmentTab', 'viewreports', Text::_('COM_VOLUNTEERS_TAB_REPORTS'));
        ?>
        <?php if ($this->acl->create_report) : ?>
            <div class="row-fluid">
                <a class="btn pull-right"
                   href="<?php echo Route::_('index.php?option=com_volunteers&task=report.add&department=' . $this->item->id) ?>">
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
                        <?php if ($this->acl->edit || ($report->created_by == $this->user->id)) : ?>
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
                            <a href="<?php echo Route::_('index.php?option=com_volunteers&view=board&id=' . $report->department) ?>"><?php echo $report->department_title; ?></a>
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
                <a href="<?php echo Route::_('index.php?option=com_volunteers&view=reports') ?>?filter_category=d.<?php echo $this->item->id; ?>"
                   class="btn">
                    <span class="icon-chevron-right"></span><?php echo Text::_('COM_VOLUNTEERS_REPORTS_BROWSE') ?>&nbsp
                </a>
            <?php endif; ?>
            <a class="btn btn-warning pull-right"
               href="<?php echo Route::_('index.php?option=com_volunteers&view=reports&filter_category=d.' . $this->item->id . '&format=feed&type=rss') ?>">
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

        echo HTMLHelper::_('uitab.addTab', 'departmentTab', 'viewreports', Text::_('COM_VOLUNTEERS_TAB_CONTACT'));
        ?>
        <?php if ($this->user->guest) : ?>
            <p class="alert alert-info">
                <?php echo Text::_('COM_VOLUNTEERS_NOTE_LOGIN_CONTACT_DEPARTMENT') ?>
            </p>
        <?php else : ?>
            <form class="form form-horizontal" name="sendmail" action="<?php echo Route::_('index.php') ?>"
                  method="post" enctype="multipart/form-data">
                <div class="control-group">
                    <label class="control-label"
                           for="to_name"><?php echo Text::_('COM_VOLUNTEERS_MESSAGE_TO') ?></label>
                    <div class="controls">
                        <input type="text" name="to_name" id="to_name" value="<?php echo $this->item->title ?>"
                               class="input-block-level" disabled="disabled"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"
                           for="from_name"><?php echo Text::_('COM_VOLUNTEERS_MESSAGE_FROM') ?></label>
                    <div class="controls">
                        <input type="text" name="from_name" id="from_name"
                               value="<?php echo($this->user->name); ?> <<?php echo($this->user->email); ?>>"
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
                              placeholder="<?php echo Text::sprintf('COM_VOLUNTEERS_MESSAGE_BODY', $this->item->title) ?>"
                              required></textarea>
                </div>
                <div class="alert alert-info">
                    <?php echo Text::sprintf('COM_VOLUNTEERS_MESSAGE_NOTICE', $this->item->title) ?>
                </div>
                <div class="control-group">
                    <input type="submit" value="<?php echo Text::_('COM_VOLUNTEERS_MESSAGE_SUBMIT') ?>" name="submit"
                           id="submitButton" class="btn btn-success pull-right"/>
                </div>

                <input type="hidden" name="option" value="com_volunteers"/>
                <input type="hidden" name="task" value="department.sendmail"/>
                <?php echo HtmlHelper::_('form.token'); ?>
            </form>
        <?php endif;
        echo HTMLHelper::_('uitab.endTab');
        ?>




      
                
           
