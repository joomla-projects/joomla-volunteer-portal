<?php

/**
 * @package    Com_Volunteers
 * @version    4.0.0
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2023 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Volunteers\Site\View\Registration;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Exception;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

use Joomla\Component\Volunteers\Site\Model\RegistrationModel;

/**
 * View class for a list of Volunteers.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected mixed $data;
    protected mixed $form;
    protected mixed $params;
    protected mixed $state;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template name
     *
     * @return void
     *
     * @since 4.0.0
     * @throws Exception
     *
     */
    public function display($tpl = null): void
    {
        /** @var RegistrationModel $model */
        $model = $this->getModel();

        $this->data   = $model->getData();
        $this->form   = $model->getForm();
        $this->state  = $model->getState();
        $this->params = $model->state->get('params');

        $errors = $model->getErrors();
        if ($errors && count($errors) > 0) {
            throw new GenericDataException(implode("\n", $errors));
        }

        parent::display($tpl);
    }
}
