<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/** @var $this HtmlView */

$user = Factory::getApplication()->getIdentity();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$canOrder = $user->authorise('core.edit.state', 'com_volunteers');
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_volunteers&task=roles.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'itemsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns');
?>

<form action="<?php echo Route::_('index.php?option=com_volunteers&view=roles'); ?>" method="post" name="adminForm"
    id="adminForm">
    <div id="j-main-container" class="j-main-container">
        <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
        <?php if (empty($this->items)): ?>
            <div class="alert alert-info">
                <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden">
                    <?php echo Text::_('INFO'); ?>
                </span>
                <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else: ?>
            <h3>
                <?php echo count($this->items); ?> roles (matching filters)
            </h3>
            <table class="table table-striped" id="itemsList">
                <thead>
                    <tr>
                        <th width="1%" class="nowrap center hidden-phone">
                            <?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
                        <th width="1%" class="hidden-phone center">
                            <?php echo HTMLHelper::_('grid.checkall'); ?>
                        </th>
                        <th width="1%" style="min-width:55px" class="nowrap center">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                        </th>
                        <th width="20%" class="role">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_VOLUNTEERS_FIELD_ROLE', 'a.name', $listDirn, $listOrder); ?>
                        </th>
                        <th class="team">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_VOLUNTEERS_FIELD_TEAM', 'a.team', $listDirn, $listOrder); ?>
                        </th>
                        <th width="1%" class="nowrap center hidden-phone">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rc = 0;

                    foreach ($this->items as $i => $it):
                        $item = (object) $it;
                        $ordering = ($listOrder == 'a.ordering');
                        $canCreate = $user->authorise('core.create', 'com_volunteers');
                        $canEdit = $user->authorise('core.edit', 'com_volunteers');
                        $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
                        $canChange = $user->authorise('core.edit.state', 'com_volunteers') && $canCheckin;
                        $rc = $rc + 1;
                        ?>
                        <tr class="row<?php echo $rc % 2; ?>" sortable-volunteer-id="<?php echo $item->id ?>">
                            <td class="order nowrap center hidden-phone">
                                <?php
                                $iconClass = '';
                                if (!$canChange) {
                                    $iconClass = ' inactive';
                                } elseif (!$saveOrder) {
                                    $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
                                }
                                ?>
                                <span class="sortable-handler<?php echo $iconClass ?>">
                                    <span class="icon-menu"></span>
                                </span>
                                <?php if ($canChange && $saveOrder): ?>
                                    <input type="text" style="display:none" name="order[]" size="5"
                                        value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
                                <?php endif; ?>
                            </td>
                            <td class="center hidden-phone">
                                <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="center">
                                <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'roles.', $canChange, 'cb'); ?>
                            </td>
                            <td class="nowrap">
                                <?php if ($item->checked_out): ?>
                                    <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'roles.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php if ($canEdit): ?>
                                    <a
                                        href="<?php echo Route::_('index.php?option=com_volunteers&task=role.edit&id=' . (int) $item->id); ?>">
                                        <?php echo $this->escape($item->title); ?></a>
                                <?php else: ?>
                                    <?php echo $this->escape($item->title); ?>
                                <?php endif; ?>
                            </td>
                            <td class="nowrap">
                                <?php if ($canEdit): ?>
                                    <a
                                        href="<?php echo Route::_('index.php?option=com_volunteers&task=team.edit&id=' . (int) $item->team); ?>">
                                        <?php echo $this->escape($item->team_title); ?></a>
                                <?php else: ?>
                                    <?php echo $this->escape($item->team_title); ?>
                                <?php endif; ?>
                            </td>
                            <td class="center hidden-phone">
                                <?php echo (int) $item->id; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php echo $this->pagination->getListFooter(); ?>
        <?php endif; ?>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>