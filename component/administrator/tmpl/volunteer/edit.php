<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');

/*$this->document->getWebAssetManager()->addScriptDeclaration("
    Joomla.submitbutton = function(task)
    {
        if (task == 'volunteer.cancel' || document.formvalidator.isValid(document.getElementById('volunteer-form'))) {
            " . $this->form->getField('joomlastory')->save() . "
            Joomla.submitform(task, document.getElementById('volunteer-form'));
        }
    };
");*/
?>

<form action="<?php echo Route::_('index.php?option=com_volunteers&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="volunteer-form" class="form-validate">

    <div class="form-inline form-inline-header">
        <?php
        echo $this->form->renderField('name');
        echo $this->form->renderField('alias');
        ?>
    </div>

    <hr>

    <div class="row">
        <div class="span9">

                <?php echo $this->form->renderFieldset('item'); ?>

        </div>
        <div class="span3">
            <?php if (Factory::getApplication()->getSession()->get('user')->authorise('core.admin')) : ?>
                    <h3><?php echo Text::_('COM_VOLUNTEERS_SECRETARY_ONLY') ?></h3>
                    <div class="control-group checkbox">
                        <div class="controls">
                            <?php echo $this->form->getInput('coc'); ?>
                            <?php echo $this->form->getLabel('coc'); ?>
                        </div>
                    </div>

            <?php endif; ?>
            <div class="form-vertical well">
                <?php echo $this->form->renderFieldset('details'); ?>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value=""/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<script>
    jQuery(document).ready(function () {
        jQuery('.location').on('change', function (e) {
            let city = jQuery('.location-city').val();
            let country = jQuery('.location-country').val();
            jQuery('.gllpSearchField').val(city + ', ' + country);
            jQuery('.gllpSearchButton').click();
        });
    });
</script>
