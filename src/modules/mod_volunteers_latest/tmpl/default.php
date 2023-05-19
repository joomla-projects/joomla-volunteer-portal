<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects
// Import CSS
try {
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->useStyle('com_volunteers.frontend');
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}
?>
<div class="well">
    <div class="page-header"><strong>
            <?php echo Text::_('COM_VOLUNTEERS_LATEST_VOLUNTEERS'); ?>
        </strong></div>
    <?php if (!empty($volunteers)) {
        foreach ($volunteers as $i => $item) : ?>
            <ul class="media-list volunteer-mods">
                <li class="media">
                    <a class="volunteer_link" href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $item->id) ?>">
                        <span class="pull-left">
                            <?php echo VolunteersHelper::image($item->image, 'small', false, $item->name); ?>
                        </span>
                        <div class="media-body">
                            <h3 class="volunteer-name">
                                <?php echo $item->name; ?>
                            </h3>
                            <p class="muted volunteer-location">
                                <span class="icon-location" aria-hidden="true"></span>
                                <?php echo VolunteersHelper::location($item->country, $item->city); ?>
                            </p>
                        </div>
                    </a>
                </li>
            </ul>
        <?php endforeach;
    }
    ?>
    <a class="volunteer-mods-btn"
        href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteers'); ?>"><?php echo Text::_('MOD_VOLUNTEERS_READ_MORE_VOLUNTEERS') ?></a>
</div>
