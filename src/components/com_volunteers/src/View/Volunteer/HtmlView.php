<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\View\Volunteer;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;
use Joomla\Component\Volunteers\Site\Model\VolunteerModel;

/**
 * View class for a single volunteer.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected mixed $state;
    protected mixed $item;
    protected mixed $form;
    protected User|null $user;

    protected string $share;

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     * @since 4.0.0
     * @throws Exception
     */
    public function display($tpl = null): void
    {
        /** @var VolunteerModel $model */

        $model = $this->getModel();


        $this->item = $model->getItem();

        $this->state       = $model->getState();
        $this->form        = $model->getForm();
        $this->user        = Factory::getApplication()->getIdentity();
        $this->item->teams = $model->getVolunteerTeams();
        $this->item->new   = Factory::getApplication()->input->getInt('new', '0');


        // Set volunteer id in session
        $session = Factory::getApplication()->getSession();

        $session->set('volunteer', $this->item->id);

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
     *
     * @since 4.0.0
     */
    protected function manipulateForm(): void
    {
        // Clear birthday field if not set
        if ($this->item->birthday == '0000-00-00') {
            $this->form->setValue('birthday', null, null);
        }

        // Make mailing address required for active team members
        if ($this->item->teams->activemember) {
            $this->form->setFieldAttribute('address', 'required', 'true');
            $this->form->setFieldAttribute('zip', 'required', 'true');
        }
    }

    /**
     * Prepares the document.
     *
     * @return  void.
     *
     * @since 4.0.0
     * @throws Exception
     */
    protected function prepareDocument(): void
    {
        // Prepare variables
        $title       = Text::_('COM_VOLUNTEERS_TITLE_VOLUNTEER') . ': ' . $this->item->name;
        $description = HtmlHelper::_('string.truncate', $this->item->intro, 160, true, false);
        $image       = VolunteersHelper::image($this->item->image, 'large', true);
        $itemURL     = Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $this->item->id);
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
        setMetaData('twitter:image', $image);

        // OpenGraph metadata
        $this->getDocument()->
        setMetaData('og:title', $title, 'property');
        $this->getDocument()->
        setMetaData('og:description', $description, 'property');
        $this->getDocument()->
        setMetaData('og:image', $image, 'property');
        $this->getDocument()->
        setMetaData('og:type', 'article', 'property');
        $this->getDocument()->
        setMetaData('og:url', $url, 'property');

        // Add to pathway
        $pathway = Factory::getApplication()->getPathway();
        $pathway->addItem($this->item->name, $itemURL);
    }
}
