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

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

HtmlHelper::_('behavior.keepalive');
HtmlHelper::_('behavior.formvalidator');
HtmlHelper::_('formbehavior.chosen', 'select');
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
<div class="team-edit">

    <form id="team"
        action="<?php echo Route::_('index.php?option=com_volunteers&task=team.save&id=' . (int) $this->item->id); ?>"
        method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
        <div class="row-fluid">

            <div class="filter-bar">
                <div class="btn-toolbar pull-right">
                    <div id="toolbar-cancel" class="btn-group">
                        <button class="btn btn-danger" onclick="Joomla.submitbutton('team.cancel')">
                            <span class="icon-cancel" aria-hidden="true"></span>
                            <?php echo Text::_('JCANCEL') ?>
                        </button>
                    </div>
                    <div id="toolbar-apply" class="btn-group">
                        <button class="btn btn-success" type="submit">
                            <span class="icon-pencil" aria-hidden="true"></span>
                            <?php echo Text::_('JSAVE') ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="page-header">
                <h1>
                    <?php echo Text::_('COM_VOLUNTEERS_TITLE_TEAMS_EDIT') ?>
                </h1>
            </div>
        </div>

        <?php echo $this->form->renderField('title'); ?>
        <?php echo $this->form->renderField('alias'); ?>

        <hr>

        <?php echo $this->form->renderField('department'); ?>
        <?php echo $this->form->renderField('status'); ?>
        <?php echo $this->form->renderField('parent_id'); ?>

        <hr>

        <?php echo $this->form->renderField('acronym'); ?>
        <?php echo $this->form->renderField('email'); ?>
        <?php echo $this->form->renderField('website'); ?>
        <?php echo $this->form->renderField('date_started'); ?>
        <?php echo $this->form->renderField('date_ended'); ?>

        <hr>

        <?php echo $this->form->renderField('description'); ?>
        <?php echo $this->form->renderField('getinvolved'); ?>

        <hr>

        <div class="row-fluid">
            <div class="btn-toolbar pull-right">
                <div id="toolbar-cancel" class="btn-group">
                    <a class="btn btn-danger"
                        href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $this->item->id) ?>">
                        <span class="icon-cancel" aria-hidden="true"></span>
                        <?php echo Text::_('JCANCEL') ?>
                    </a>
                </div>
                <div id="toolbar-apply" class="btn-group">
                    <button class="btn btn-success" type="submit">
                        <span class="icon-pencil" aria-hidden="true"></span>
                        <?php echo Text::_('JSAVE') ?>
                    </button>
                </div>
            </div>
        </div>

        <input type="hidden" name="option" value="com_volunteers" />
        <input type="hidden" name="task" value="team.save" />
        <?php echo HtmlHelper::_('form.token'); ?>
    </form>
</div>
