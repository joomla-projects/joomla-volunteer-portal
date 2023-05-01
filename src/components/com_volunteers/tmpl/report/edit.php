<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

HtmlHelper::_('behavior.keepalive');
HtmlHelper::_('behavior.formvalidator');
HtmlHelper::_('formbehavior.chosen', 'select');

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'report.cancel' || document.formvalidator.isValid(document.getElementById('report'))) {
			Joomla.submitform(task, document.getElementById('report'));
		}
	}
");
?>

<div class="report-edit">

    <form id="report" action="<?php echo Route::_('index.php?option=com_volunteers&task=report.save&id=' . (int) $this->item->id); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
        <div class="row-fluid">
            <div class="filter-bar">

                <div class="btn-toolbar pull-right">
                    <div id="toolbar-cancel" class="btn-group">
                        <button class="btn btn-danger" onclick="Joomla.submitbutton('report.cancel')">
                            <span class="icon-cancel"></span> <?php echo Text::_('JCANCEL') ?>
                        </button>
                    </div>
                    <div id="toolbar-apply" class="btn-group">
                        <button class="btn btn-success" type="submit">
                            <span class="icon-pencil"></span> <?php echo Text::_('JSAVE') ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="page-header">
                <h1><?php echo Text::_('COM_VOLUNTEERS_TITLE_REPORTS_EDIT') ?></h1>
            </div>
        </div>

        <?php if ($this->item->department) : ?>
            <?php echo $this->form->renderField('department'); ?>
        <?php endif; ?>
        <?php if ($this->item->team) : ?>
            <?php echo $this->form->renderField('team'); ?>
        <?php endif; ?>

        <hr>

        <?php echo $this->form->renderField('title'); ?>
        <?php echo $this->form->renderField('alias'); ?>

        <hr>

        <?php echo $this->form->renderField('created'); ?>

        <hr>

        <?php echo $this->form->renderField('description'); ?>

        <hr>

        <div class="row-fluid">
            <div class="btn-toolbar pull-right">
                <div id="toolbar-cancel" class="btn-group">
                    <a class="btn btn-danger" href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $this->item->team . '#reports') ?>">
                        <span class="icon-cancel"></span> <?php echo Text::_('JCANCEL') ?>
                    </a>
                </div>
                <div id="toolbar-apply" class="btn-group">
                    <button class="btn btn-success" type="submit">
                        <span class="icon-pencil"></span> <?php echo Text::_('JSAVE') ?>
                    </button>
                </div>
            </div>
        </div>

        <input type="hidden" name="option" value="com_volunteers"/>
        <input type="hidden" name="task" value="report.save"/>
        <?php echo HtmlHelper::_('form.token'); ?>
    </form>
</div>
