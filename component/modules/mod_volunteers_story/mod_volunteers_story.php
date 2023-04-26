<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Component\Volunteers\Administrator\Model\VolunteersModel;

defined('_JEXEC') or die;

try {
    $app = Factory::getApplication();
} catch (Exception $e) {
    die('Cannot access Joomla Application!');
}
$model = new VolunteersModel();
$model->setCodeModel(true);
$model->setState('list.limit', 1);
$model->setState('list.ordering', 'rand()');
$model->setState('filter.image', 1);
$model->setState('filter.joomlastory', 1);

$items = $model->getItems();

$story = $items[0];

require ModuleHelper::getLayoutPath('mod_volunteers_story');
