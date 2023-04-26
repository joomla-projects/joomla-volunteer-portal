<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;


HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');

/*
$this->document->getWebAssetManager()->addScriptDeclaration("
    Joomla.submitbutton = function(task)
    {
        if (task == 'member.cancel' || document.formvalidator.isValid(document.getElementById('member-form'))) {
            Joomla.submitform(task, document.getElementById('member-form'));
        }
    };
");*/
?>

<form action="<?php echo Route::_('index.php?option=com_volunteers&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="member-form" class="form-validate">

    <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <hr>

    <div class="row">
        <div class="span9">
            <?php echo $this->form->renderFieldset('item'); ?>
        </div>
        <div class="span3">
            <?php echo $this->form->renderFieldset('details'); ?>
        </div>
    </div>

    <input type="hidden" name="task" value=""/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
