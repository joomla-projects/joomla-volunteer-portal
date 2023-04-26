<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\Controller;

use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

/**
 * Positions list controller class.
 *
 * @since 4.0.0
 */
class PositionsController extends AdminController
{
    /**
     * Proxy for getModel.
     *
     * @param   string  $name    Optional. Model name
     * @param   string  $prefix  Optional. Class prefix
     * @param   array   $config  Optional. Configuration array for model
     *
     * @return  object  The Model
     *
     * @since   4.0.0
     */
    public function getModel($name = 'Position', $prefix = 'Administrator', $config = array()): object
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }
}
