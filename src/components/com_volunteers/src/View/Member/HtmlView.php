<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\View\Member;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

use Joomla\CMS\User\User;
use Joomla\Component\Volunteers\Site\Model\VolunteerModel;

/**
 * View to edit a member.
 *
 * @since 4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected mixed $state;
    protected mixed $item;
    protected mixed $form;
    protected User|null $user;
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function display($tpl = null): void
    {
        /** @var MemberModel $model */

        $model      = $this->getModel();
        $this->item = $model->getItem();

        $this->state = $model->getState();
        $this->form  = $model->getForm();

        $this->user  = Factory::getApplication()->getIdentity();

        $errors = $model->getErrors();
        if ($errors && count($errors) > 0) {
            throw new GenericDataException(implode("\n", $errors));
        }

        // Manipulate form
        $this->manipulateForm();

        // Prepare document
        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * Manipulates the form.
     *
     * @return  void.
     * @since 4.0.0
     * @throws Exception
     */
    protected function manipulateForm(): void
    {
        $app          = Factory::getApplication();
        $jinput       = $app->input;
        $memberId     = $jinput->getInt('id');
        $departmentId = (int) $app->getUserState('com_volunteers.edit.member.departmentid');
        $teamId       = (int) $app->getUserState('com_volunteers.edit.member.teamid');

        // Disable fields
        $this->form->setFieldAttribute('department', 'readonly', 'true');
        $this->form->setFieldAttribute('team', 'readonly', 'true');

        // Clear date ended field if not set
        if ($this->item->date_ended == '0000-00-00') {
            $this->form->setValue('date_ended', null, null);
        }

        // If editing existing member
        if ($memberId) {
            $this->form->setFieldAttribute('volunteer', 'readonly', 'true');
            $this->form->setFieldAttribute('position', 'readonly', 'true');

            if ($departmentId) {
                $this->form->removeField('role');
            }
        } else {
            $this->form->setValue('department', null, $departmentId);
            $this->form->setValue('team', null, $teamId);
            $this->form->setValue('date_started', null, Factory::getDate());
            $this->item->department = $departmentId;
            $this->item->team       = $teamId;
        }
    }

    /**
     * Prepares the document.
     *
     * @return  void.
     * @since 4.0.0
     */
    protected function prepareDocument(): void
    {
        // Prepare variables
        $title = Text::_('COM_VOLUNTEERS_TITLE_MEMBERS_EDIT');

        // Set meta
        $this->getDocument()->
        setTitle($title);
    }
}
