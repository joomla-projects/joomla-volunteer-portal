<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;



HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');

?>

<form action="<?php echo Route::_('index.php?option=com_volunteers&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="team-form" class="form-validate">

    <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <hr>

    <div class="row">
        <div class="span9">


                <?php echo $this->form->renderFieldset('item'); ?>
            <h3><?php echo Text::_('COM_VOLUNTEERS_FIELD_ACL'); ?></h3>
            <p><?php echo Text::_('COM_VOLUNTEERS_FIELD_ACL_DESC'); ?></p>

            <?php echo $this->form->renderFieldset('acl'); ?>
        </div>

        <div class="span3">
            <?php echo $this->form->renderFieldset('details'); ?>
        </div>
    </div>

    <input type="hidden" name="task" value=""/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
