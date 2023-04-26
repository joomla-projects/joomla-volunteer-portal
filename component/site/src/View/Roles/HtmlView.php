<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\View\Roles;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Volunteers\Site\Model\RolesModel;

/**
 * View class for a list of roles.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected array $items;
    protected Pagination $pagination;
    protected CMSObject $state;


    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @since 4.0.0
     * @throws Exception
     *
     */
    public function display($tpl = null)
    {
        /** @var RolesModel $model */

        $model       = $this->getModel();
        $this->items = $model->getOpenRoles();

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
        $title   = Text::_('COM_VOLUNTEERS_TITLE_ROLESOPEN');
        $itemURL = Route::_('index.php?option=com_volunteers&view=roles');
        $url     = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $itemURL;

        // Set meta
        $this->document->setTitle($title);

        // Twitter Card metadata
        $this->document->setMetaData('twitter:title', $title);
        $this->document->setMetaData('twitter:image', Uri::getInstance()->toString(['scheme', 'host', 'port']) . '/images/volunteers-help-wanted-twitter.jpg');

        // OpenGraph metadata
        $this->document->setMetaData('og:title', $title, 'property');
        $this->document->setMetaData('og:image', Uri::getInstance()->toString(['scheme', 'host', 'port']) . '/images/volunteers-help-wanted.jpg', 'property');
        $this->document->setMetaData('og:type', 'article', 'property');
        $this->document->setMetaData('og:url', $url, 'property');
    }
}
