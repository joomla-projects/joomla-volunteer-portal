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

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

// Get reports
try {
    $app = Factory::getApplication();
} catch (Exception $e) {
    die('Cannot access Joomla Application!');
}
$model = $app->bootComponent('com_volunteers')->getMVCFactory()->createModel('Volunteers', 'Administrator', ['ignore_request' => true]);
$model->setState('list.limit', 5);
$model->setState('list.ordering', 'a.created');
$model->setState('list.direction', 'desc');
$model->setState('filter.image', 1);

$volunteers = $model->getItems();

require ModuleHelper::getLayoutPath('mod_volunteers_latest', 'default');
