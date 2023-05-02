<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\View\Volunteer;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit a volunteer.
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
        /** @var VolunteerModel $model */
        $model       = $this->getModel();
        $this->state = $model->getState();
        $this->item  = $model->getItem();
        $this->form  = $model->getForm();

        $errors = $model->getErrors();

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

        // Set toolbar title
        ToolbarHelper::title($isNew ? Text::_('COM_VOLUNTEERS') . ': ' . Text::_('COM_VOLUNTEERS_TITLE_VOLUNTEERS_NEW') : Text::_('COM_VOLUNTEERS') . ': ' . Text::_('COM_VOLUNTEERS_TITLE_VOLUNTEERS_EDIT'), 'joomla');

        if (!$checkedOut && ($canDo->get('core.edit') || $canDo->get('core.create'))) {
            ToolbarHelper::apply('volunteer.apply');
            ToolbarHelper::save('volunteer.save');
        }

        if (!$checkedOut && $canDo->get('core.create')) {
            ToolbarHelper::save2new('volunteer.save2new');
        }

        if (!$isNew && $canDo->get('core.create')) {
            ToolbarHelper::save2copy('volunteer.save2copy');
        }

        if (empty($this->item->id)) {
            ToolbarHelper::cancel('volunteer.cancel');
        } else {
            if ($this->state->params->get('save_history', 0) && $user->authorise('core.edit')) {
                ToolbarHelper::versions('com_volunteers.volunteer', $this->item->id);
            }

            ToolbarHelper::cancel('volunteer.cancel', 'JTOOLBAR_CLOSE');
        }
    }

    /**
     * Manipulates the form.
     *
     * @return  void.
     * @since 4.0.0
     */
    protected function manipulateForm()
    {
        $this->form->removeField('password1');
        $this->form->removeField('password2');
    }
}
