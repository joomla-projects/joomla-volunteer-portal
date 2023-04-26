<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\View\Department;

defined('_JEXEC') or die;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Volunteers\Administrator\Model\DepartmentModel;
/**
 * View to edit a department.
 *
 * @since 4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected CMSObject $state;
    protected mixed $item;
    protected mixed $form;
/**
     * Display the view
     *
     * @param   string  $tpl  Template
     *
     * @return  void
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function display($tpl = null)
    {
        /** @var DepartmentModel $model */
        $model = $this->getModel();
        $this->state = $model->getState();
        $this->item  = $model->getItem();
        $this->form  = $model->getForm();
        $errors = $model->getErrors();
        if ($errors && count($errors) > 0) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since 4.0.0
     * @throws Exception
     */
    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);
        $user       = Factory::getApplication()->getSession()->get('user');
        $isNew      = ($this->item->id == 0);
        $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        $canDo      = ContentHelper::getActions('com_volunteers');
        ToolbarHelper::title($isNew ? Text::_('COM_VOLUNTEERS') . ': ' . Text::_('COM_VOLUNTEERS_TITLE_DEPARTMENTS_NEW') : Text::_('COM_VOLUNTEERS') . ': ' . Text::_('COM_VOLUNTEERS_TITLE_DEPARTMENTS_EDIT'), 'joomla');
        if (!$checkedOut && ($canDo->get('core.edit') || $canDo->get('core.create'))) {
            ToolbarHelper::apply('department.apply');
            ToolbarHelper::save('department.save');
        }

        if (!$checkedOut && $canDo->get('core.create')) {
            ToolbarHelper::save2new('department.save2new');
        }

        if (!$isNew && $canDo->get('core.create')) {
            ToolbarHelper::save2copy('department.save2copy');
        }

        if (empty($this->item->id)) {
            ToolbarHelper::cancel('department.cancel');
        } else {
            if ($this->state->params->get('save_history', 0) && $user->authorise('core.edit')) {
                ToolbarHelper::versions('com_volunteers.department', $this->item->id);
            }

            ToolbarHelper::cancel('department.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
