<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/** @var $this \Joomla\Component\Volunteers\Site\View\Team\HtmlView */
?>
<?php if ($this->acl->edit) : ?>
    <div class="row-fluid">
        <a class="btn pull-right" href="<?php echo Route::_('index.php?option=com_volunteers&task=role.add&team=' . $this->item->id); ?>">
            <span class="icon-new" aria-hidden="true"></span>
            <?php echo Text::_('COM_VOLUNTEERS_ROLE_ADD'); ?>
        </a>
    </div>
    <hr>
<?php endif; ?>
<?php if ($this->item->roles) : ?>
    <?php foreach ($this->item->roles as $role) : ?>
        <div class="row-fluid">
            <div class="team well">
                <div class="row-fluid">
                    <div class="span8">
                        <?php if ($this->acl->edit) : ?>
                            <a class="btn btn-small pull-right" href="<?php echo Route::_('index.php?option=com_volunteers&task=role.delete&id=' . $role->id); ?>">
                                <span class="icon-delete" aria-hidden="true"></span>
                                <?php echo Text::_('COM_VOLUNTEERS_DELETE'); ?>
                            </a>
                            <a class="btn btn-small pull-right" href="<?php echo Route::_('index.php?option=com_volunteers&task=role.edit&id=' . $role->id); ?>">
                                <span class="icon-delete" aria-hidden="true"></span>
                                <?php echo Text::_('COM_VOLUNTEERS_EDIT'); ?>
                            </a>
                        <?php endif; ?>
                        <h2><?php echo $role->title; ?></h2>
                        <p><?php echo $role->description; ?></p>
                        <?php if ($role->open) : ?>
                            <a class="btn" data-toggle="tab" onclick="document.getElementById('teamsTab').activateTab(document.getElementById('viewcontact'));">
                                <span class="icon-chevron-right" aria-hidden="true"></span>
                                <?php echo Text::_('COM_VOLUNTEERS_ROLE_APPLY'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="span4">
                        <div class="members">
                            <?php if (!empty($role->volunteers)) : ?>
                                <?php foreach ($role->volunteers as $rolevolunteer) : ?>
                                    <a class="tip hasTooltip" title="<?php echo $rolevolunteer->volunteer_name; ?>"
                                        href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $rolevolunteer->volunteer); ?>">
                                        <?php echo VolunteersHelper::image($rolevolunteer->volunteer_image, 'small', false, $rolevolunteer->volunteer_image); ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if ($role->open) : ?>
                                <a data-toggle="tab" class="all-members"  onclick="document.getElementById('teamsTab').activateTab(document.getElementById('viewgetinvolved'));">
                                    <span class="all"><?php echo Text::_('COM_VOLUNTEERS_YOU'); ?></span><span class="number">?</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <div class="row-fluid">
        <p class="alert alert-info"><?php echo Text::_('COM_VOLUNTEERS_NOTE_NO_ROLES'); ?></p>
    </div>
<?php endif;
