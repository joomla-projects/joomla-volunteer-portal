<?php

/**
 * @version    4.0.0
 * @package    Com_Volunteers
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2023 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;

defined('_JEXEC') or die('Restricted access');
/* @var $displayData array */

if ($displayData->item->active) {
    echo HTMLHelper::_('uitab.addTab', 'teamsTab', 'viewmembers', Text::_('COM_VOLUNTEERS_TAB_MEMBERS'));
    if ($displayData->acl->edit) {
        echo '<div class="row-fluid">
        <a class="btn pull-right" href="' . Route::_('index.php?option=com_volunteers&task=member.add&team=' . $displayData->item->id) . '">
            <span class="icon-new"></span> ' . Text::_('COM_VOLUNTEERS_MEMBER_ADD') . '
        </a>
    </div>
    <hr>';
    };
    if ($displayData->item->members->active) {
        echo '<table class="table table-striped table-hover table-vertical-align">
    <thead>
    <th width="30%">' . Text::_('COM_VOLUNTEERS_FIELD_VOLUNTEER') . '</th>
    <th width="20%">' . Text::_('COM_VOLUNTEERS_FIELD_POSITION') . '</th>
    <th>' . Text::_('COM_VOLUNTEERS_FIELD_ROLE') . '</th>
    <th width="12%">' . Text::_('COM_VOLUNTEERS_FIELD_DATE_STARTED') . '</th>';
        if ($displayData->acl->edit) {
            echo '<th width="10%" class="center">' . Text::_('COM_VOLUNTEERS_FIELD_ADDRESS') . '</th>
        <th width="10%" class="center">' . Text::_('COM_VOLUNTEERS_FIELD_NDA') . '</th>
        <th width="10%"></th>';
        };
        echo '</thead>';
        echo '<tbody>';
        foreach ($displayData->item->members->active as $volunteer) {
            echo '<tr>
            <td class="volunteer-image">
                <a class="pull-left"
                   href="' . Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $volunteer->volunteer) . '">' .
                VolunteersHelper::image($volunteer->volunteer_image, 'small', false, $volunteer->volunteer_image) . '
                </a>
                <a href="' . Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $volunteer->volunteer) . '">' .
                $volunteer->volunteer_name . '
                </a>
                <span class="muted volunteer-location">
                                            <span class="icon-location"></span>' . VolunteersHelper::location($volunteer->volunteer_country) . '
                                        </span>
            </td>
            <td>' . $volunteer->position_title . '
            </td>
            <td>' .
                $volunteer->role_title . '
            </td>
            <td>' . VolunteersHelper::date($volunteer->date_started, 'M Y') . '
            </td>';
            if ($displayData->acl->edit) {
                echo '<td class="center">';
                if ($volunteer->address) {
                    echo ' <span class="icon-checkbox-checked"></span>';
                } else {
                    echo ' <span class="icon-checkbox-unchecked"></span>';
                };
                echo '</td>
                <td class="center">';
                if ($volunteer->nda) {
                    echo '<span class="icon-checkbox-checked"></span>';
                } else {
                    echo '<span class="icon-checkbox-unchecked"></span>';
                };
                echo '</td>
                <td>
                    <a class="btn btn-small pull-right" href="' . Route::_('index.php?option=com_volunteers&task=member.edit&id=' . $volunteer->id) . '">
                        <span class="icon-edit"></span> ' . Text::_('COM_VOLUNTEERS_EDIT') . '
                    </a>
                </td>';
            };
            echo '</tr>';
        }
        echo '</tbody>
</table>';
    };
};
