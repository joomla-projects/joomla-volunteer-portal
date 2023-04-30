<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects
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
    <div class="page-header">
        <h1><?php echo Text::_('COM_VOLUNTEERS_TITLE_ROLESOPEN') ?></h1>
    </div>
    <p class="lead"><?php echo Text::_('COM_VOLUNTEERS_TITLE_ROLESOPEN_INTRO') ?></p>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php foreach ($this->items as $team => $roles) : ?>
            <div class="well well">
                <h2 style="margin-top: 0;">
                    <a href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $roles[0]->team) ?>" id="<?php echo OutputFilter::stringURLSafe($team); ?>">
                        <?php echo $team; ?>
                    </a>
                </h2>
                <table class="table table-striped table-hover table-vertical-align">
                    <thead>
                    <th><?php echo Text::_('COM_VOLUNTEERS_FIELD_ROLE') ?></th>
                    <th width="300px"></th>
                    </thead>
                    <tbody>
                    <?php foreach ($roles as $role) : ?>
                        <tr>
                            <td>
                                <h3><?php echo $role->title; ?></h3>
                                <?php echo $role->description; ?>
                            </td>
                            <td>
                                <a class="btn btn-small pull-right" href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $role->team . '&tab=contact') ?>">
                                    <span class="icon-mail"></span> <?php echo Text::_('COM_VOLUNTEERS_TAB_CONTACT') ?>
                                </a>
                                <a class="btn btn-small pull-right" href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $role->team . '&tab=getinvolved') ?>">
                                    <span class="icon-chevron-right"></span> <?php echo Text::_('COM_VOLUNTEERS_ROLE_APPLY') ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
</div>
