<?php

/**
 * @package    Volunteer Portal
 *
 * @copyright  (C) 2023 Open Source Matters, Inc.  <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Administrator\View\Setupdemo;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;
use SimpleXMLElement;

/**
 * View class for a single Copyvolunteer3data.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The item object
     *
     * @var    object
     * @since  4.0.0
     */
    protected mixed $item;
/**
     * The model state
     *
     * @var    CMSObject
     * @since  4.0.0
     */
    protected CMSObject $state;
/**
     * Component Parameters
     *
     * @var    Registry
     * @since  4.0.0
     */
    protected $params = null;
/**
     * Migration SQL
     *
     * @var    SimpleXMLElement
     * @since  4.0.0
     */
    protected SimpleXMLElement $migrate_xml;
/**
     * Action Task
     *
     * @var    string
     * @since  4.0.0
     */
    protected string $task;
/**
     * Add the page title and toolbar.
     *
     * @since  4.0.0
     * @throws Exception
     */
    private function addToolbar(): void
    {
        ToolBarHelper::title('Setup Demo Menu');
        $user = Factory::getApplication()->getIdentity();

        if (
            $user->authorise('core.admin', 'com_volunteer')
            || $user->authorise(
                'core.options',
                'com_volunteer'
            )
        ) {
            $bar = Toolbar::getInstance();


            ToolbarHelper::preferences('com_volunteer');
        }
    }

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @since  4.0.0
     * @throws  Exception
     */
    public function display($tpl = null): void
    {
        $this->state       = $this->get('State');
        $this->item        = $this->get('Item');
        $this->params      = ComponentHelper::getParams('com_volunteer');
        $app  = Factory::getApplication();
        $input = $app->input->getInputForRequestMethod();
        $this->task = $input->get('task', '');
        $this->addToolbar();
        parent::display($tpl);
    }
}
