<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\View\Team;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\StringHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;
use Joomla\Component\Volunteers\Site\Model\TeamModel;
use stdClass;

/**
 * View to edit a team.
 *
 * @since 4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected CMSObject $state;
    protected mixed $item;
    protected mixed $form;
    protected User|null $user;
    protected stdClass $acl;


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
    public function display($tpl = null)
    {

        /** @var TeamModel $model */

        $model      = $this->getModel();
        $this->item = $model->getItem();

        $this->state          = $model->getState();
        $this->form           = $model->getForm();
        $this->user           = Factory::getApplication()->getIdentity();
        $this->item->reports  = $model->getTeamReports();
        $this->item->subteams = $model->getTeamSubteams();
        $this->item->members  = $model->getTeamMembers();
        $this->item->roles    = $model->getTeamRoles();
        $this->acl            = VolunteersHelper::acl('team', $this->item->id);

        // Set team id in session
        Factory::getApplication()->getSession()->set('team', $this->item->id);

        // Active / inactive
        $this->item->active = ($this->item->date_ended == '0000-00-00');

        $errors = $model->getErrors();
        if ($errors && count($errors) > 0) {
            throw new GenericDataException(implode("\n", $errors));
        }

        // Manipulate form
        $this->_manipulateForm();

        // Prepare document
        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * Manipulates the form.
     *
     * @return  void.
     *
     * @since 4.0.0
     * @throws Exception
     */
    protected function _manipulateForm()
    {
        // Manipulate frontend edit form
        $app    = Factory::getApplication();
        $teamId = $app->input->getInt('id');

        // Clear date ended field if not set
        if ($this->item->date_ended == '0000-00-00') {
            $this->form->setValue('date_ended', null, null);
        }

        // If editing existing team
        if ($teamId) {
            if (!$this->acl->edit_department) {
                $this->form->setFieldAttribute('department', 'readonly', 'true');
                $this->form->setFieldAttribute('status', 'readonly', 'true');
            }
        } else {
            $departmentId = (int) $app->getUserState('com_volunteers.edit.team.departmentid');
            $teamId       = (int) $app->getUserState('com_volunteers.edit.team.teamid');
            $this->form->setValue('department', null, $departmentId);
            $this->form->setValue('parent_id', null, $teamId);
            $this->form->setValue('date_started', null, Factory::getDate());
            $this->form->setFieldAttribute('department', 'readonly', 'true');

            if ($teamId) {
                $this->form->setFieldAttribute('parent_id', 'readonly', 'true');
            }
        }
    }

    /**
     * Prepares the document.
     *
     * @return  void.
     * @since 4.0.0
     * @throws Exception
     */
    protected function _prepareDocument()
    {
        $layout = Factory::getApplication()->input->get('layout');

        if ($layout == 'edit') {
            // Prepare variables
            $title = Text::_('COM_VOLUNTEERS_TITLE_TEAMS_EDIT');

            // Set meta
            $this->document->setTitle($title);

            return;
        }

        // Prepare variables
        $title       = $this->item->title;
        $description = StringHelper::truncate($this->item->description, 160, true, false);
        $image       = 'https://cdn.joomla.org/images/joomla-org-og.jpg';
        $itemURL     = Route::_('index.php?option=com_volunteers&view=team&id=' . $this->item->id);
        $url         = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $itemURL;

        // Set meta
        $this->document->setTitle($title);
        $this->document->setDescription($description);

        // Twitter Card metadata
        $this->document->setMetaData('twitter:title', $title);
        $this->document->setMetaData('twitter:description', $description);
        $this->document->setMetaData('twitter:image', $image);

        // OpenGraph metadata
        $this->document->setMetaData('og:title', $title, 'property');
        $this->document->setMetaData('og:description', $description, 'property');
        $this->document->setMetaData('og:image', $image, 'property');
        $this->document->setMetaData('og:type', 'article', 'property');
        $this->document->setMetaData('og:url', $url, 'property');

        // Add to pathway
        $pathway = Factory::getApplication()->getPathway();
        $pathway->addItem($this->item->title, $itemURL);

        // Add the RSS link.
        $props = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
        $route = Route::_('index.php?option=com_volunteers&view=reports&filter_category=t.' . $this->item->id . '&format=feed&type=rss', false);
        $this->document->addHeadLink($route, 'alternate', 'rel', $props);

        // Add the ATOM link.
        $props = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
        $route = Route::_('index.php?option=com_volunteers&view=reports&filter_category=t.' . $this->item->id . '&format=feed&type=atom', false);
        $this->document->addHeadLink($route, 'alternate', 'rel', $props);
    }
}
