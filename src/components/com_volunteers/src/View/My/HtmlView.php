<?php

/**
 * @package    Com_Volunteers
 * @version    4.0.0
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2023 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\View\My;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\Component\Volunteers\Administrator\Extension\VolunteersComponent;
use Joomla\Component\Volunteers\Administrator\Model\VolunteerModel;

/**
 * View class for a list of Volunteers.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @since 4.0.0
     * @throws Exception
     *
     */
    public function display($tpl = null): void
    {
        $app = Factory::getApplication();
        /** @var VolunteersComponent $extension */
        $extension = $app->bootComponent('com_volunteers');
        /** @var VolunteerModel $model */
        $model = $extension->getMVCFactory()->createModel('Volunteer', 'Administrator', ['ignore_request' => true]);

        $user        = Factory::getApplication()->getIdentity();
        $userId      = (int) $user->id;
        $volunteerId = (int) $model->getVolunteerId($userId);

        if ($volunteerId) {
            Factory::getApplication()->redirect(Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $volunteerId, false));
        }
        parent::display($tpl);
    }
}
