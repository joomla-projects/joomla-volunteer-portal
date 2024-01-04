<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\View\Report;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\StringHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;
use Joomla\Component\Volunteers\Site\Model\ReportModel;
use Joomla\Component\Volunteers\Site\Model\VolunteerModel;
use stdClass;

/**
 * View to edit a report.
 *
 * @since 4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected mixed $state;
    protected mixed $item;
    protected mixed $form;
    protected User|null $user;
    protected stdClass $acl;

    protected string $share;
    protected $volunteer;

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
    public function display($tpl = null): void
    {
        /** @var ReportModel $model */

        $model      = $this->getModel();
        $this->item = $model->getItem();

        $this->state = $model->getState();

        $this->form = $model->getForm();
        $this->user = Factory::getApplication()->getIdentity();
        // Load volunteer data for new report
        if (!$this->item->id) {
            $this->volunteer = $model->getVolunteer();
        }

        if ($this->item->department && ($this->item->department_parent_id == 0)) {
            $this->acl        = VolunteersHelper::acl('department', $this->item->department);
            $this->item->link = Route::_('index.php?option=com_volunteers&view=board&id=' . $this->item->department);
            $this->item->name = $this->item->department_title;
        } elseif ($this->item->department) {
            $this->acl        = VolunteersHelper::acl('department', $this->item->department);
            $this->item->link = Route::_('index.php?option=com_volunteers&view=department&id=' . $this->item->department);
            $this->item->name = $this->item->department_title;
        } elseif ($this->item->team) {
            $this->acl        = VolunteersHelper::acl('team', $this->item->team);
            $this->item->link = Route::_('index.php?option=com_volunteers&view=team&id=' . $this->item->team);
            $this->item->name = $this->item->team_title;
        }

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
        $app      = Factory::getApplication();
        $jinput   = $app->input;
        $reportId = $jinput->getInt('id');

        // Disable fields
        $this->form->setFieldAttribute('department', 'readonly', 'true');
        $this->form->setFieldAttribute('team', 'readonly', 'true');

        // If editing existing report
        if ($reportId) {
            //$this->form->setFieldAttribute('volunteer', 'readonly', 'true');
        } else {
            $departmentId = (int) $app->getUserState('com_volunteers.edit.report.departmentid');
            $teamId       = (int) $app->getUserState('com_volunteers.edit.report.teamid');
            $this->form->setValue('department', null, $departmentId);
            $this->form->setValue('team', null, $teamId);
            $this->form->setValue('created', null, Factory::getDate());
            $this->item->department = $departmentId;
            $this->item->team       = $teamId;
        }
    }

    /**
     * Prepares the document.
     *
     * @return  void.
     * @since 4.0.0
     * @throws Exception
     *
     */
    protected function prepareDocument(): void
    {
        $layout = Factory::getApplication()->input->get('layout');

        if ($layout == 'edit') {
            // Prepare variables
            $title = Text::_('COM_VOLUNTEERS_TITLE_REPORTS_EDIT');

            // Set meta
            $this->getDocument()->
            setTitle($title);

            return;
        }

        // Prepare variables
        $typeTitle   = ($this->item->team) ? $this->item->team_title : $this->item->department_title;
        $title       = $this->item->title . ' - ' . $typeTitle;
        $description = StringHelper::truncate($this->item->description, 160, true, false);
        $itemURL     = Route::_('index.php?option=com_volunteers&view=report&id=' . $this->item->id);
        $url         = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $itemURL;

        // Set meta
        $this->getDocument()->
        setTitle($title);
        $this->getDocument()->
        setDescription($description);

        // Twitter Card metadata
        $this->getDocument()->
        setMetaData('twitter:title', $title);
        $this->getDocument()->
        setMetaData('twitter:description', $description);
        $this->getDocument()->
        setMetaData('twitter:image', Uri::base() . 'images/reports-twitter.jpg');

        // OpenGraph metadata
        $this->getDocument()->
        setMetaData('og:title', $title, 'property');
        $this->getDocument()->
        setMetaData('og:description', $description, 'property');
        $this->getDocument()->
        setMetaData('og:image', Uri::base() . 'images/reports-og.jpg', 'property');
        $this->getDocument()->
        setMetaData('og:type', 'article', 'property');
        $this->getDocument()->
        setMetaData('og:url', $url, 'property');

        // Share Buttons
        $layout = new FileLayout('joomlarrssb');
        $data   = (object) [
            'title'            => $title,
            'image'            => Uri::base() . 'images/reports-og.jpg',
            'url'              => $url,
            'text'             => $description,
            'displayEmail'     => true,
            'displayFacebook'  => true,
            'displayTwitter'   => true,
            'displayGoogle'    => false,
            'displayLinkedin'  => true,
            'displayPinterest' => true,
            'shorten'          => true,
            'shortenKey'       => ComponentHelper::getParams('com_volunteers')->get('yourlsapikey'),
        ];
        $this->share = $layout->render($data);

        // Add to pathway
        $pathway = Factory::getApplication()->getPathway();
        if ($this->item->team) {
            $pathway->addItem($this->item->team_title, Route::_('index.php?option=com_volunteers&view=team&id=' . $this->item->team));
        } elseif ($this->item->department) {
            $pathway->addItem($this->item->department_title, Route::_('index.php?option=com_volunteers&view=department&id=' . $this->item->department));
        }
        $pathway->addItem($this->item->title, $itemURL);
    }
}
