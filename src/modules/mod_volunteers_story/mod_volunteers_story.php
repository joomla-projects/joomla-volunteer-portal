<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Component\Volunteers\Administrator\Extension\VolunteersComponent;
use Joomla\Component\Volunteers\Administrator\Model\VolunteersModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

$app = Factory::getApplication();
/** @var VolunteersComponent $extension */
$extension = $app->bootComponent('com_volunteers');
/** @var VolunteersModel $model */
$model = $extension->getMVCFactory()->createModel('Volunteers', 'Administrator', ['ignore_request' => true]);
$model->setState('list.limit', 1);
$model->setState('list.ordering', 'rand()');
$model->setState('filter.image', 1);
$model->setState('filter.joomlastory', 1);

$items = $model->getItems();

$story = $items[0];

require ModuleHelper::getLayoutPath('mod_volunteers_story');
