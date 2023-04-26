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

// Get reports
try {
    $app = Factory::getApplication();
} catch (Exception $e) {
    die('Cannot access Joomla Application!');
}
$model = new VolunteersModel();
$model->setCodeModel(true);
$model->setState('list.limit', 5);
$model->setState('list.ordering', 'a.created');
$model->setState('list.direction', 'desc');
$model->setState('filter.image', 1);

$volunteers = $model->getItems();

require ModuleHelper::getLayoutPath('mod_volunteers_latest', 'default');
