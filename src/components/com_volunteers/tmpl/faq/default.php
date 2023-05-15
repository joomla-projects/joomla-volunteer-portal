<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects
HTMLHelper::_('jquery.framework');
// Import CSS
try {
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->useStyle('com_volunteers.j3template')->useStyle('com_volunteers.frontend');
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}


?>

<div class="row-fluid">
    <div class="page-header">
            <?php echo Text::_('COM_VOLUNTEERS_TITLE_FAQ'); ?>
        </h1>
    </div>
    <?php echo Text::_('COM_VOLUNTEERS_FAQ_TEXT'); ?>
</div>

