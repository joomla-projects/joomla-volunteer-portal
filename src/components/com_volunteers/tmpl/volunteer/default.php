<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/** @var \Joomla\Component\Volunteers\Site\View\Volunteer\HtmlView $this */

// Import CSS
try {
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->useStyle('com_volunteers.frontend');
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}
?>
<?php if ($this->item->new) { ?>
    <div class="alert alert-success">
        <h1 class="vol_h1">
            <?php echo Text::_('COM_VOLUNTEERS_PROFILE_NEW_COMPLETED') ?>
        </h1>

        <p class="lead">
            <?php echo Text::_('COM_VOLUNTEERS_WELCOME') ?>
        </p>
    </div>
<?php } ?>

<div class="row profile">
    <div class="col-3 volunteer-image">
        <?php echo VolunteersHelper::image($this->item->image, 'large', false, $this->item->name); ?>

        <?php if ($this->item->certification) { ?>
            <div class="volunteer-certificated">
                <a href="https://certification.joomla.org/certified-user-directory/<?php echo ($this->item->certification) ?>"
                    target="_blank">
                    <img src="https://certification.joomla.org/images/Badges/3x_certified_administrator_badge.png"
                        class="img-responsive" />
                </a>
            </div>
        <?php } ?>
    </div>
    <div class="col-9">
        <div class="filter-bar">
            <?php if (($this->user->id == $this->item->user_id) && $this->item->user_id) { ?>
                <a class="volunteers_btn pull-right" href="https://identity.joomla.org/profile?layout=edit">
                    <span class="icon-edit" aria-hidden="true"></span>
                    <?php echo Text::_('COM_VOLUNTEERS_TITLE_VOLUNTEERS_EDIT_MY') ?>
                </a>
            <?php } ?>
        </div>
        <div class="page-header">
            <h1 class="vol_h1">
                <?php echo $this->item->name; ?>
            </h1>
        </div>

        <?php if ($this->item->{'city-location'} || $this->item->country) { ?>
            <p class="muted">
                <span class="icon-location" aria-hidden="true"></span>
                <?php echo VolunteersHelper::location($this->item->country, $this->item->{'city-location'}); ?>
            </p>
        <?php } ?>

        <p class="lead">
            <?php echo ($this->item->intro) ?>
        </p>


        <div class="btn-group">
            <?php if ($this->item->joomlaforum) { ?>
                <a class="volunteers_btn btn-joomlaforum" target="_blank"
                    href="https://forum.joomla.org/memberlist.php?mode=viewprofile&u=<?php echo ($this->item->joomlaforum) ?>">
                    <span class="icon-joomla" aria-hidden="true"></span>
                    <?php echo Text::_('COM_VOLUNTEERS_CONNECT_JOOMLAFORUM') ?>
                </a>
            <?php } ?>
            <?php if ($this->item->joomladocs) { ?>
                <a class="volunteers_btn btn-joomladocs" target="_blank"
                    href="https://docs.joomla.org/User:<?php echo ($this->item->joomladocs) ?>">
                    <span class="icon-joomla" aria-hidden="true"></span>
                    <?php echo Text::_('COM_VOLUNTEERS_CONNECT_JOOMLADOCS') ?>
                </a>
            <?php } ?>
        </div>

        <p>
        <div class="btn-group">
            <?php if ($this->item->github) { ?>
                <a class="volunteers_btn btn-gtihub" target="_blank" href="https://github.com/<?php echo ($this->item->github) ?>">
                    <span class="icon-github" aria-hidden="true"></span>
                    <span class="hidden-phone">
                        <?php echo Text::_('COM_VOLUNTEERS_CONNECT_GITHUB') ?>
                    </span>
                </a>
            <?php } ?>
            <?php if ($this->item->crowdin) { ?>
                <a class="volunteers_btn btn-crowdin" target="_blank"
                    href="https://crowdin.com/profile/<?php echo ($this->item->crowdin) ?>">
                    <span class="icon-comments-2" aria-hidden="true"></span>
                    <span class="hidden-phone">
                        <?php echo Text::_('COM_VOLUNTEERS_CONNECT_CROWDIN') ?>
                    </span>
                </a>
            <?php } ?>
            <?php if ($this->item->stackexchange) { ?>
                <a class="volunteers_btn btn-stackexchange" target="_blank"
                    href="https://stackexchange.com/users/<?php echo ($this->item->stackexchange) ?>">
                    <span class="icon-comments-2" aria-hidden="true"></span>
                    <span class="hidden-phone">
                        <?php echo Text::_('COM_VOLUNTEERS_CONNECT_STACKEXCHANGE') ?>
                    </span>
                </a>
            <?php } ?>
            <?php if ($this->item->joomlastackexchange) { ?>
                <a class="volunteers_btn btn-joomlastackexchange" target="_blank"
                    href="https://joomla.stackexchange.com/users/<?php echo ($this->item->joomlastackexchange) ?>">
                    <span class="icon-comments-2" aria-hidden="true"></span>
                    <span class="hidden-phone">
                        <?php echo Text::_('COM_VOLUNTEERS_CONNECT_JOOMLASTACKEXCHANGE') ?>
                    </span>
                </a>
            <?php } ?>
        </div>
        </p>

        <p>
        <div class="btn-group">
            <?php if ($this->item->website && ($this->item->website != 'http://')) { ?>
                <a class="volunteers_btn" target="_blank" href="<?php echo ($this->item->website) ?>">
                    <span class="icon-link" aria-hidden="true"></span>
                    <span class="hidden-phone">
                        <?php echo Text::_('COM_VOLUNTEERS_CONNECT_WEBSITE') ?>
                    </span>
                </a>
            <?php } ?>
            <?php if ($this->item->twitter) { ?>
                <a class="volunteers_btn btn-twitter" target="_blank" href="https://twitter.com/<?php echo ($this->item->twitter) ?>">
                    <span class="icon-twitter" aria-hidden="true"></span>
                    <span class="hidden-phone">
                        <?php echo Text::_('COM_VOLUNTEERS_CONNECT_TWITTER') ?>
                    </span>
                </a>
            <?php } ?>
            <?php if ($this->item->facebook) { ?>
                <a class="volunteers_btn btn-facebook" target="_blank"
                    href="https://www.facebook.com/<?php echo ($this->item->facebook) ?>">
                    <span class="icon-facebook" aria-hidden="true"></span>
                    <span class="hidden-phone">
                        <?php echo Text::_('COM_VOLUNTEERS_CONNECT_FACEBOOK') ?>
                    </span>
                </a>
            <?php } ?>
            <?php if ($this->item->linkedin) { ?>
                <a class="volunteers_btn btn-linkedin" target="_blank"
                    href="https://www.linkedin.com/in/<?php echo ($this->item->linkedin) ?>">
                    <span class="icon-linkedin" aria-hidden="true"></span>
                    <span class="hidden-phone">
                        <?php echo Text::_('COM_VOLUNTEERS_CONNECT_LINKEDIN') ?>
                    </span>
                </a>
            <?php } ?>
        </div>
        </p>
    </div>
</div>

<br>

<div class="row">
    <div class="col-12">
        <?php
        echo HTMLHelper::_('uitab.startTabSet', 'volunteerTab', ['active' => 'viewteamsinvolved', 'recall' => true, 'breakpoint' => 768]);
        if ($this->item->teams->active) {
            echo HTMLHelper::_('uitab.addTab', 'volunteerTab', 'viewteamsinvolved', Text::_('COM_VOLUNTEERS_TAB_TEAMSINVOLVED'));
            ?>
            <table class="table table-striped table-hover table-vertical-align">
                <thead>
                    <th width="30%">
                        <?php echo Text::_('COM_VOLUNTEERS_FIELD_TEAM') ?>
                    </th>
                    <th width="20%">
                        <?php echo Text::_('COM_VOLUNTEERS_FIELD_POSITION') ?>
                    </th>
                    <th>
                        <?php echo Text::_('COM_VOLUNTEERS_FIELD_ROLE') ?>
                    </th>
                    <th width="12%">
                        <?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_STARTED') ?>
                    </th>
                </thead>
                <tbody>
                    <?php foreach ($this->item->teams->active as $team) { ?>
                        <tr>
                            <td>
                                <a href="<?php echo $team->link; ?>"><?php echo $team->name; ?></a>
                            </td>
                            <td>
                                <?php echo ($team->position_title) ?>
                            </td>
                            <td>
                                <?php echo ($team->role_title) ?>
                            </td>
                            <td>
                                <?php echo VolunteersHelper::date($team->date_started, 'M Y'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
            echo HTMLHelper::_('uitab.endTab');
        }
        if ($this->item->teams->honorroll) {
            echo HTMLHelper::_('uitab.addTab', 'volunteerTab', 'viewhonorroll', Text::_('COM_VOLUNTEERS_TAB_HONORROLL'));
            ?>
            <table class="table table-striped table-hover table-vertical-align">
                <thead>
                    <th width="30%">
                        <?php echo Text::_('COM_VOLUNTEERS_FIELD_TEAM') ?>
                    </th>
                    <th width="20%">
                        <?php echo Text::_('COM_VOLUNTEERS_FIELD_POSITION') ?>
                    </th>
                    <th>
                        <?php echo Text::_('COM_VOLUNTEERS_FIELD_ROLE') ?>
                    </th>
                    <th width="12%">
                        <?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_STARTED') ?>
                    </th>
                    <th width="12%">
                        <?php echo Text::_('COM_VOLUNTEERS_FIELD_DATE_ENDED') ?>
                    </th>
                </thead>
                <tbody>
                    <?php foreach ($this->item->teams->honorroll as $team) { ?>
                        <tr>
                            <td>
                                <a href="<?php echo $team->link; ?>"><?php echo $team->name; ?></a>
                            </td>
                            <td>
                                <?php echo ($team->position_title) ?>
                            </td>
                            <td>
                                <?php echo ($team->role_title) ?>
                            </td>
                            <td>
                                <?php echo VolunteersHelper::date($team->date_started, 'M Y'); ?>
                            </td>
                            <td>
                                <?php echo VolunteersHelper::date($team->date_ended, 'M Y'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
            echo HTMLHelper::_('uitab.endTab');
        }
        if ($this->item->joomlastory) {
            echo HTMLHelper::_('uitab.addTab', 'volunteerTab', 'viewjoomlastory', Text::_('COM_VOLUNTEERS_TAB_JOOMLASTORY'));
            echo (nl2br($this->item->joomlastory));
            echo HTMLHelper::_('uitab.endTab');
        }

        if ($this->user->id != $this->item->user_id) {
            echo HTMLHelper::_('uitab.addTab', 'volunteerTab', 'viewcontact', Text::_('COM_VOLUNTEERS_TAB_CONTACT'));

            if ($this->user->guest) { ?>
                <p class="alert alert-info">
                    <?php echo Text::_('COM_VOLUNTEERS_NOTE_LOGIN_CONTACT_VOLUNTEER') ?>
                </p>
                <?php
            } else { ?>
                <form class="form form-horizontal" name="sendmail" action="<?php echo Route::_('index.php') ?>" method="post"
                    enctype="multipart/form-data">
                    <div class="control-group">
                        <label class="control-label" for="to_name">
                            <?php echo Text::_('COM_VOLUNTEERS_MESSAGE_TO') ?>
                        </label>
                        <div class="controls">
                            <input type="text" name="to_name" id="to_name"
                                value="<?php echo $this->escape($this->item->name); ?>" class="input-block-level"
                                disabled="disabled" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="from_name">
                            <?php echo Text::_('COM_VOLUNTEERS_MESSAGE_FROM') ?>
                        </label>
                        <div class="controls">
                            <input type="text" name="from_name" id="from_name"
                                value="<?php echo $this->escape($this->user->name); ?> <<?php echo $this->escape($this->user->email); ?>>"
                                class="input-block-level" disabled="disabled" />
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls col-12">
                            <input type="text" name="subject" id="subject" class="input-block-level"
                                placeholder="<?php echo Text::_('COM_VOLUNTEERS_MESSAGE_SUBJECT') ?>" required />
                        </div>
                    </div>
                    <div class="control-group">
                        <textarea rows="10" name="message" id="message" class="input-block-level"
                            placeholder="<?php echo Text::sprintf('COM_VOLUNTEERS_MESSAGE_BODY', $this->escape($this->item->name)) ?>"
                            required></textarea>
                    </div>
                    <div class="alert alert-info">
                        <?php echo Text::sprintf('COM_VOLUNTEERS_MESSAGE_NOTICE', $this->escape($this->item->name)) ?>
                    </div>
                    <div class="control-group">
                        <input type="submit" value="<?php echo Text::_('COM_VOLUNTEERS_MESSAGE_SUBMIT') ?>" name="submit"
                            id="submitButton" class="volunteers_btn btn-success pull-right" />
                    </div>

                    <input type="hidden" name="option" value="com_volunteers" />
                    <input type="hidden" name="task" value="volunteer.sendmail" />
                    <?php echo HtmlHelper::_('form.token'); ?>
                </form>
                <a class="volunteers_btn btn-danger js-reportspam" data-volunteer="<?php echo $this->item->id; ?>"
                    data-success="<?php echo Text::_('COM_VOLUNTEERS_SPAM_REPORT_SUCCESS') ?>">
                    <span class="icon-warning" aria-hidden="true"></span>
                    <?php echo Text::_('COM_VOLUNTEERS_SPAM_REPORT') ?>
                </a>
            <?php }

            echo HTMLHelper::_('uitab.endTab');
        } ?>










        <script type="text/javascript">


            // Report Spam Button
            let reportspambutton = jQuery('.js-reportspam');
            if (reportspambutton) {
                reportspambutton.click(function (e) {
                    e.preventDefault();
                    let item = jQuery(this),
                        request = {
                            'option': 'com_ajax',
                            'plugin': 'reportspam',
                            'format': 'json',
                            'volunteer': item.attr('data-volunteer')
                        };

                    jQuery.ajax({
                        type: 'POST',
                        data: request,
                        success: function (response) {
                            item.removeClass('btn-danger').addClass('btn-success').html('<span class="icon-thumbs-up"></span> ' + item.attr('data-success'));
                        }
                    });
                });
            }
