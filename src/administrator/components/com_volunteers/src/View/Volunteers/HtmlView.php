<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\View\Volunteers;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Volunteers\Administrator\Model\VolunteersModel;

/**
 * View class for a list of volunteers.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * An array of items
     *
     * @var  array
     */
    protected array $items;

    /**
     * The pagination object
     *
     * @var  \Joomla\CMS\Pagination\Pagination
     */
    protected Pagination $pagination;

    /**
     * The model state
     *
     * @var   \Joomla\CMS\Object\CMSObject
     */
    protected CMSObject $state;

    /**
     * Form object for search filters
     *
     * @var  \Joomla\CMS\Form\Form
     */
    public Form $filterForm;

    /**
     * The active search filters
     *
     * @var  array
     */
    public array $activeFilters;

    /**
     * Is this view an Empty State
     *
     * @var   boolean
     * @since 4.0.0
     */
    private $isEmptyState = false;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template name
     *
     * @return  void
     *
     * @since 4.0.0
     * @throws Exception
     *
     */
    public function display($tpl = null)
    {
        /** @var VolunteersModel $model */
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
        $user  = Factory::getApplication()->getIdentity();

        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance();

        ToolbarHelper::title(Text::_('COM_VOLUNTEERS') . ': ' . Text::_('COM_VOLUNTEERS_TITLE_VOLUNTEERS'), 'joomla');

        if ($canDo->get('core.create')) {
            $toolbar->addNew('volunteer.add');
        }

        if (!$this->isEmptyState && $canDo->get('core.edit.state')) {
            /** @var  DropdownButton $dropdown */
            $dropdown = $toolbar->dropdownButton('status-group', 'JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            $childBar->publish('volunteers.publish')->listCheck(true);
            $childBar->unpublish('volunteers.unpublish')->listCheck(true);
            $childBar->archive('volunteers.archive')->listCheck(true);

            if ($user->authorise('core.admin')) {
                $childBar->checkin('volunteers.checkin');
            }

            if ($this->state->get('filter.published') != -2) {
                $childBar->trash('volunteers.trash')->listCheck(true);
            }
        }

        if (!$this->isEmptyState && $this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
            $toolbar->delete('volunteers.delete', 'JTOOLBAR_EMPTY_TRASH')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

        if ($user->authorise('core.admin', 'com_volunteers') || $user->authorise('core.options', 'com_volunteers')) {
            $toolbar->preferences('com_volunteers');
        }
        if ($canDo->get('core.edit')) {
            ToolbarHelper::custom('volunteers.resetspam', 'refresh', 'refresh2.png', 'COM_VOLUNTEERS_TOOLBAR_RESET', false);
        }
    }
}
