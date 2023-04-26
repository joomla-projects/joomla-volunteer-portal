<?php

/**
 * @package    Com_Volunteers
 * @version    4.0.0
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2023 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\View\Board;

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
use Joomla\Component\Volunteers\Site\Model\BoardModel;
use stdClass;

/**
 * View class Volunteer Board Display.
 *
 * @since  4.0.0
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
        /** @var BoardModel $model */

        $model      = $this->getModel();
        $this->item = $model->getItem();

        $this->state         = $model->getState();
        $this->form          = $model->getForm();
        $this->user          = Factory::getApplication()->getIdentity();
        $this->item->reports = $model->getDepartmentReports();
        $this->item->members = $model->getDepartmentMembers();


        $this->acl = VolunteersHelper::acl('department', $this->item->id);


        // Set department id in session
        Factory::getApplication()->getSession()->set('department', $this->item->id);

        $errors = $model->getErrors();
        if ($errors && count($errors) > 0) {
            throw new GenericDataException(implode("\n", $errors));
        }
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }


        if ($this->_layout == 'edit') {
            $authorised = $this->user->authorise('core.create', 'com_volunteers');

            if ($authorised !== true) {
                throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
            }
        }

        $this->_prepareDocument();

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
    protected function _prepareDocument()
    {

        $title       = $this->item->title;
        $description = StringHelper::truncate($this->item->description, 160, true, false);
        $image       = 'https://cdn.joomla.org/images/joomla-org-og.jpg';
        $itemURL     = Route::_('index.php?option=com_volunteers&view=board&id=' . $this->item->id);
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
        $route = Route::_('index.php?option=com_volunteers&view=reports&filter_category=d.' . $this->item->id . '&format=feed&type=rss', false);
        $this->document->addHeadLink($route, 'alternate', 'rel', $props);

        // Add the ATOM link.
        $props = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
        $route = Route::_('index.php?option=com_volunteers&view=reports&filter_category=d.' . $this->item->id . '&format=feed&type=atom', false);
        $this->document->addHeadLink($route, 'alternate', 'rel', $props);
    }
}
