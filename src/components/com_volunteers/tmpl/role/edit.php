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
		if (task == 'role.cancel' || document.formvalidator.isValid(document.getElementById('role'))) {
			Joomla.submitform(task, document.getElementById('role'));
		}
	}
");
?>

<div class="role-edit">

    <form id="role" action="<?php echo Route::_('index.php?option=com_volunteers&task=role.save&id=' . (int) $this->item->id); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
        <div class="row-fluid">
            <div class="filter-bar">
                <div class="btn-toolbar pull-right">
                    <div id="toolbar-cancel" class="btn-group">
                        <button class="btn btn-danger" onclick="Joomla.submitbutton('role.cancel')">
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
                <h1><?php echo Text::_('COM_VOLUNTEERS_TITLE_ROLES_EDIT') ?></h1>
            </div>
        </div>

        <?php echo $this->form->renderField('team'); ?>

        <hr>

        <?php echo $this->form->renderField('title'); ?>
        <?php echo $this->form->renderField('open'); ?>
        <?php echo $this->form->renderField('description'); ?>

        <hr>

        <div class="row-fluid">
            <div class="btn-toolbar pull-right">
                <div id="toolbar-cancel" class="btn-group">
                    <a class="btn btn-danger" href="<?php echo Route::_('index.php?option=com_volunteers&view=team&id=' . $this->item->team) ?>">
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
        <input type="hidden" name="task" value="role.save"/>
        <?php echo HtmlHelper::_('form.token'); ?>
    </form>
</div>
