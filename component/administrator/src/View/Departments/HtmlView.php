<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\View\Departments;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Volunteers\Administrator\Model\DepartmentsModel;

/**
 * View class for a list of departments.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected array $items;
    protected Pagination $pagination;
    protected CMSObject $state;
    public Form $filterForm;
    public array $activeFilters;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template name
     *
     * @return  void
     *
     * @throws Exception
     *
     * @since 4.0.0
     */
    public function display($tpl = null)
    {
        /** @var DepartmentsModel $model */
        $model               = $this->getModel();
        $this->state         = $model->getState();
        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();
        $errors              = $model->getErrors();

        if ($errors && count($errors) > 0) {
            throw new GenericDataException(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   4.0.0
     * @throws Exception
     */
    private function addToolbar(): void
    {
        $canDo = ContentHelper::getActions('com_volunteers');
        $user  = Factory::getApplication()->getSession()->get('user');

        ToolbarHelper::title(Text::_('COM_VOLUNTEERS') . ': ' . Text::_('COM_VOLUNTEERS_TITLE_DEPARTMENTS'), 'joomla');

        if ($canDo->get('core.create')) {
            ToolbarHelper::addNew('department.add');
        }

        if ($canDo->get('core.edit')) {
            ToolbarHelper::editList('department.edit');
        }

        if ($canDo->get('core.edit.state')) {
            ToolbarHelper::publish('departments.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('departments.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            ToolbarHelper::archiveList('departments.archive');
            ToolbarHelper::checkin('departments.checkin');
        }

        if ($canDo->get('core.delete')) {
            ToolbarHelper::deleteList('', 'departments.delete', 'JTOOLBAR_EMPTY_TRASH');
        } elseif ($canDo->get('core.edit.state')) {
            ToolbarHelper::trash('departments.trash');
        }

        if ($user->authorise('core.admin', 'com_volunteers') || $user->authorise('core.options', 'com_volunteers')) {
            ToolbarHelper::preferences('com_volunteers');
        }
    }
}
