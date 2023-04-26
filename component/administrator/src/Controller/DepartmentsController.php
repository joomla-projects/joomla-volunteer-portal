<?php

/**
 * @version    4.0.0
 * @package    Com_Volunteers
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2023 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\Controller;

defined('_JEXEC') or die;

/**
 * Departments list controller class.
 *
 * @since 4.0.0
 */
class DepartmentsController extends AdminController
{
    /**
     * Proxy for getModel
     *
     * @param   string $name   The model name. Optional.
     * @param   string $prefix The class prefix. Optional.
     * @param   array  $config The array of possible config values. Optional.
     *
     * @return  object  The model.
     *
     * @since 4.0.0
     */
    public function getModel($name = 'Department', $prefix = 'Administrator', $config = array())
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }
}
