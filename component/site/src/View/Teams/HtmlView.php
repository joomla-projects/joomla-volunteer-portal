<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\View\Teams;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Volunteers\Site\Model\TeamsModel;

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

        $model            = $this->getModel();
        $this->state      = $model->getState();
        $this->items      = $model->getItems();
        $this->pagination = $model->getPagination();


        $errors = $model->getErrors();
        if ($errors && count($errors) > 0) {
            throw new GenericDataException(implode("\n", $errors));
        }


        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document.
     *
     * @return  void.
     *
     * @since 4.0.0
     */
    protected function _prepareDocument()
    {
        // Prepare variables
        $title   = ($this->state->get('filter.groups')) ? Text::_('COM_VOLUNTEERS_TITLE_GROUPS') : Text::_('COM_VOLUNTEERS_TITLE_TEAMS');
        $image   = 'https://cdn.joomla.org/images/joomla-org-og.jpg';
        $itemURL = Route::_('index.php?option=com_volunteers&view=teams');
        $url     = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $itemURL;

        // Set meta
        $this->document->setTitle($title);

        // Twitter Card metadata
        $this->document->setMetaData('twitter:title', $title);
        $this->document->setMetaData('twitter:image', $image);

        // OpenGraph metadata
        $this->document->setMetaData('og:title', $title, 'property');
        $this->document->setMetaData('og:image', $image, 'property');
        $this->document->setMetaData('og:type', 'article', 'property');
        $this->document->setMetaData('og:url', $url, 'property');
    }
}
