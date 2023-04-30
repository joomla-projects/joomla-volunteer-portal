<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Volunteers\Administrator\Model\VolunteerModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Volunteers Plugin class
 * @since 4.0.0
 */
class PlgSystemVolunteers extends CMSPlugin
{
    /**
     * Application object.
     *
     * @var    CMSApplication
     * @since  1.0
     */
    protected $app;

    /**
     * Application object.
     *
     * @var    JDatabaseDriver
     * @since  1.0.0
     */
    protected $db;

    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $autoloadLanguage = true;


    /**
     * Check if volunteer filled in all required fields
     *
     * @return  boolean  True on success
     * @since 4.0.0
     * @throws Exception
     */
    public function onAfterRender(): bool
    {
        // Run on frontend only

        if ($this->app->isClient('administrator')) {
            return true;
        }

        // Get variables
        $view = $this->app->input->getString('view');
        $task = $this->app->input->getString('task');

        // Check if volunteer needs to update profile
        $update = Factory::getApplication()->getSession()->get('updateprofile');

        if ($update && $view != 'volunteer' && $task != 'volunteer.edit') {
            $model = $this->app->bootComponent('com_volunteers')->getMVCFactory()->createModel('Volunteer', 'Administrator', ['ignore_request' => true]);

            $userId      = Factory::getApplication()->getSession()->get('user')->get('id');
            $volunteerId = (int) $model->getVolunteerId($userId);

            // Redirect to profile
            $this->loadLanguage('com_volunteers', JPATH_ADMINISTRATOR);
            $this->app->enqueueMessage(Text::_('COM_VOLUNTEERS_PROFILE_ACTIVEMEMBERFIELDS'), 'warning');
            $this->app->redirect('index.php?option=com_volunteers&task=volunteer.edit&id=' . $volunteerId);
        }

        return true;
    }

    /**
     * If the user is not logged in, redirect to the login component
     *
     * @return bool
     * @since 4.0.0
     * @throws Exception
     */
    public function onAfterRoute(): bool
    {

        // Run on frontend only
        $isAdmin = !Factory::getApplication()->isClient('site');
        if ($isAdmin) {
            return true;
        }

        // Get variables
        $option = $this->app->input->getString('option');
        $view   = $this->app->input->getString('view');
        $layout = $this->app->input->getString('layout');
        $id     = $this->app->input->getInt('id');

        // Check if volunteer url is correct
        if ($option == 'com_volunteers' && $view == 'volunteer' && $layout != 'edit') {
            $itemURL    = Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $id);
            $correctURL = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $itemURL;
            $currentURL = str_replace("&", "&amp;", Uri::getInstance()->toString());
            if ($correctURL != $currentURL) {
                $this->app->redirect(Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $id, false), 301);
            }
        }

        // Check if this is the volunteers own profile
        if ($option == 'com_volunteers' && $view == 'volunteer') {
            $model = $this->app->bootComponent('com_volunteers')->getMVCFactory()->createModel('Volunteer', 'Administrator', ['ignore_request' => true]);

            $userId      = Factory::getApplication()->getSession()->get('user')->get('id');
            $volunteerId = (int) $model->getVolunteerId($userId);

            // Change active menu for own profile
            if ($volunteerId == $id) {
                $menu     = $this->app->getMenu();
                $menuItem = $menu->getItems('link', 'index.php?option=com_volunteers&view=my', true);
                $menu->setActive($menuItem->id);
            }
        }

        return true;
    }
}
