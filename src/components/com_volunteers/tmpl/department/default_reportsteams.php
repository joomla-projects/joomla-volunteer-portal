<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/** @var \Joomla\Component\Volunteers\Site\View\Department\HtmlView $this */
?>
<div class="tab-pane" id="reportsTeams">
    <?php if ($this->item->reportsTeams) : ?>
        <?php foreach ($this->item->reportsTeams as $report) : ?>
            <div class="row report">
                <div class="col-2 volunteer-image">
                    <a
                        href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $report->volunteer_id) ?>">
                        <?php echo VolunteersHelper::image($report->volunteer_image, 'large', false, is_null($report->volunteer_name) ? '' : $report->volunteer_name); ?>
                    </a>
                </div>
                <div class="col-10">
                    <?php if ($this->acl->edit || ($report->created_by == $this->user->id)) : ?>
                        <a class="volunteers_btn btn-small pull-right"
                           href="<?php echo Route::_('index.php?option=com_volunteers&task=report.edit&id=' . $report->id) ?>">
                            <span class="icon-edit" aria-hidden="true"></span>
                            <?php echo Text::_('COM_VOLUNTEERS_EDIT') ?>
                        </a>
                    <?php endif; ?>
                    <h2 class="vol_h2">
                        <a
                            href="<?php echo Route::_('index.php?option=com_volunteers&view=report&id=' . $report->id) ?>">
                            <?php echo $report->title; ?>
                        </a>
                    </h2>
                    <p class="muted">
                        <?php echo Text::_('COM_VOLUNTEERS_BY') ?>
                        <a
                            href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $report->volunteer_id) ?>"><?php echo $report->volunteer_name; ?></a>
                        <?php echo Text::_('COM_VOLUNTEERS_ON') ?>
                        <?php echo VolunteersHelper::date($report->created, 'Y-m-d H:i'); ?>
                        <?php echo Text::_('COM_VOLUNTEERS_IN') ?>
                        <?php if ($report->department) : ?>
                            <a
                                href="<?php echo Route::_('index.php?option=com_volunteers&view=department&id=' . $report->department) ?>"><?php echo $report->department_title; ?></a>
                        <?php elseif ($report->team) : ?>
                            <a
                                href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $report->team) ?>"><?php echo $report->team_title; ?></a>
                        <?php endif; ?>
                    </p>
                    <p>
                        <?php echo HtmlHelper::_('string.truncate', strip_tags(trim($report->description)), 300); ?>
                    </p>
                    <a href="<?php echo Route::_('index.php?option=com_volunteers&view=report&id=' . $report->id) ?>"
                       class="volunteers_btn">
                        <?php echo Text::_('COM_VOLUNTEERS_READ_MORE') ?>
                        &nbsp;<?php echo ($report->title); ?>
                    </a>
                </div>
            </div>
            <hr>
        <?php endforeach; ?>

        <a href="<?php echo Route::_('index.php?option=com_volunteers&view=reports') ?>" class="volunteers_btn">
            <span class="icon-chevron-right" aria-hidden="true"></span>
            <?php echo Text::_('COM_VOLUNTEERS_READ_MORE_REPORTS'); ?>
            &nbsp
        </a>

    <?php else : ?>
        <div class="row">
            <p class="alert alert-info">
                <?php echo Text::_('COM_VOLUNTEERS_NOTE_NO_REPORTS') ?>
            </p>
        </div>
    <?php endif; ?>
</div>

