<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\View\Teams;

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
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for a list of teams.
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
     * @since 4.0.0
     * @throws Exception
     *
     */
    public function display($tpl = null)
    {
        /** @var TeamsModel $model */
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

        $state = $this->get('State');
        $canDo = ContentHelper::getActions('com_volunteers');
        $user  = Factory::getApplication()->getSession()->get('user');

        // Set toolbar title
        ToolbarHelper::title(Text::_('COM_VOLUNTEERS') . ': ' . Text::_('COM_VOLUNTEERS_TITLE_TEAMS'), 'joomla');

        if ($canDo->get('core.create')) {
            ToolbarHelper::addNew('team.add');
        }

        if ($canDo->get('core.edit')) {
            ToolbarHelper::editList('team.edit');
        }

        if ($canDo->get('core.edit.state')) {
            ToolbarHelper::publish('teams.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('teams.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            ToolbarHelper::archiveList('teams.archive');
            ToolbarHelper::checkin('teams.checkin');
        }

        if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
            ToolbarHelper::deleteList('', 'teams.delete', 'JTOOLBAR_EMPTY_TRASH');
        } elseif ($canDo->get('core.edit.state')) {
            ToolbarHelper::trash('teams.trash');
        }

        if ($user->authorise('core.admin', 'com_volunteers') || $user->authorise('core.options', 'com_volunteers')) {
            ToolbarHelper::preferences('com_volunteers');
        }
    }
}
