<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\View\Reports;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\Component\Volunteers\Site\Model\ReportsModel;

/**
 * View class for a list of reports.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected array $items;
    protected Pagination $pagination;
    protected mixed $state;

    protected User|null $user;

    protected mixed $category;


    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void  A string if successful, otherwise a Error object.
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function display($tpl = null): void
    {
        /** @var ReportsModel $model */

        $model            = $this->getModel();
        $this->state      = $model->getState();
        $this->items      = $model->getItems();
        $this->pagination = $model->getPagination();
        $this->user       = Factory::getApplication()->getIdentity();
        $this->category   = $model->getCategory();

        $errors = $model->getErrors();
        if ($errors && count($errors) > 0) {
            throw new GenericDataException(implode("\n", $errors));
        }


        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     *
     * @return void
     *
     * @since 4.0.0
     * @throws Exception
     *
     */
    protected function prepareDocument(): void
    {
        // Prepare variables
        $title   = Text::_('COM_VOLUNTEERS_TITLE_REPORTS');
        $image   = 'https://cdn.joomla.org/images/joomla-org-og.jpg';
        $itemURL = Route::_('index.php?option=com_volunteers&view=reports');
        $url     = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $itemURL;

        // Set meta
        $this->getDocument()->
        setTitle($title);

        // Twitter Card metadata
        $this->getDocument()->
        setMetaData('twitter:title', $title);
        $this->getDocument()->
        setMetaData('twitter:image', $image);

        // OpenGraph metadata
        $this->getDocument()->
        setMetaData('og:title', $title, 'property');
        $this->getDocument()->
        setMetaData('og:image', $image, 'property');
        $this->getDocument()->
        setMetaData('og:type', 'article', 'property');
        $this->getDocument()->
        setMetaData('og:url', $url, 'property');

        // Add the RSS link.
        $props = ['type' => 'application/rss+xml', 'title' => 'RSS 2.0'];
        $route = Route::_('index.php?option=com_volunteers&view=reports&filter_category=&format=feed&type=rss');
        $this->getDocument()->addHeadLink($route, 'alternate', 'rel', $props);

        // Add the ATOM link.
        $props = ['type' => 'application/atom+xml', 'title' => 'Atom 1.0'];
        $route = Route::_('index.php?option=com_volunteers&view=reports&filter_category=&format=feed&type=atom');
        $this->getDocument()->addHeadLink($route, 'alternate', 'rel', $props);
    }
}
