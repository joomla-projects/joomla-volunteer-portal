<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

/**
 * Story - Object holding information
 * @var object $story
 */

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
<div class="well">
<div class="page-header"><strong><?php echo Text::_('COM_VOLUNTEERS_JOOMLASTORY'); ?></strong></div>
<ul class="media-list">
    <li class="media">
        <a class="pull-left"
           href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $story->id) ?>">
            <?php echo VolunteersHelper::image($story->image, 'small', false, $story->name); ?>
        </a>
        <div class="media-body">
            <h3 class="media-heading">
                <a href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $story->id) ?>">

                    <?php echo $story->name; ?>
                </a>
            </h3>
            <p class="muted">
                <span class="icon-location"></span> <?php echo VolunteersHelper::location($story->country, $story->city); ?>
            </p>
        </div>
    </li>
    <li class="media">
        <p><?php echo HtmlHelper::_('string.truncate', strip_tags(trim($story->joomlastory)), 500); ?></p>
        <a href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $story->id) ?>#joomlastory"
           class="btn">
            <span class="icon-chevron-right"></span><?php echo Text::_('MOD_VOLUNTEERS_STORY_READ_MORE_JOOMLASTORY') ?>
        </a>
    </li>
</ul>
</div>
