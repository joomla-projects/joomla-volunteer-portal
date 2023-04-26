<?php

/**
 * @package        JED
 *
 * @copyright  (C) 2023 Open Source Matters, Inc.  <https://www.joomla.org>
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Menus\Administrator\Table\MenuTable;
use Joomla\Component\Menus\Administrator\Table\MenuTypeTable;

defined('_JEXEC') or die;
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <p>Clicking GO below will create a new front end menu called jvpdemo and populate it with menu links to
                Volunteers Component</p>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-12"><a href="<?php echo $_SERVER['REQUEST_URI'] . '&task=GO'; ?>"
            <button class="btn btn-primary" type="button">Go</button>
            </a></div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($this->task === "GO") {
                try {
                    $db    = Factory::getContainer()->get('DatabaseDriver');
                    $query = $db->getQuery(true)
                        ->select('extension_id')
                        ->from($db->quoteName('#__extensions'))
                        ->where('name = "com_volunteers"');
                    $db->setQuery($query);
                    $extension_id = $db->loadResult();
                    echo "Extension com_volunteers found - " . $extension_id . '<br/>';
                    $mtt = new MenuTypeTable($db);
                    $mtt->menutype    = 'jvpdemo';
                    $mtt->title       = 'Joomla Volunteer Portal Demo Menu';
                    $mtt->description = '';
                    $mtt->client_id   = 0;
                    $mtt->store();
                    echo "<br/>Created jvpdemo Menu Type<br/><br/>";
                    $mi = new MenuTable($db);
                    $menuitems[] = array(
                        'title'  => 'Home',
                        'alias'  => 'homejvp',
                        'path'   => 'homejvp',
                        'link'   => 'index.php?option=com_volunteers&view=home',
                        'params' => '{"menu-anchor_title":"","menu-anchor_css":"","menu_icon_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","robots":""}');
                    $menuitems[] = array(
                        'title'  => 'Board of Directors',
                        'alias'  => 'board-of-directors',
                        'path'   => 'board-of-directors',
                        'link'   => 'index.php?option=com_volunteers&view=board&id=3',
                        'params' => '{"menu-anchor_title":"","menu-anchor_css":"","menu_icon_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","robots":""}');
                    $menuitems[] = array(
                        'title'  => 'Departments',
                        'alias'  => 'departments',
                        'path'   => 'departments',
                        'link'   => 'index.php?option=com_volunteers&view=departments',
                        'params' => '{"menu-anchor_title":"","menu-anchor_css":"","menu_icon_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","robots":""}');
                    $menuitems[] = array(
                        'title'  => 'Teams',
                        'alias'  => 'teams',
                        'path'   => 'teams',
                        'link'   => 'index.php?option=com_volunteers&view=teams&id=50',
                        'params' => '{"menu-anchor_title":"","menu-anchor_css":"","menu_icon_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","robots":""}');
                    $menuitems[] = array(
                        'title'  => 'Groups',
                        'alias'  => 'groups',
                        'path'   => 'groups',
                        'link'   => 'index.php?option=com_volunteers&view=teams&id=58',
                        'params' => '{"menu-anchor_title":"","menu-anchor_css":"","menu_icon_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","robots":""}');
                    $menuitems[] = array(
                        'title'  => 'Joomlers',
                        'alias'  => 'joomlers',
                        'path'   => 'joomlers',
                        'link'   => 'index.php?option=com_volunteers&view=volunteers',
                        'params' => '{"menu-anchor_title":"","menu-anchor_css":"","menu_icon_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","robots":""}');
                    $menuitems[] = array(
                        'title'  => 'Reports',
                        'alias'  => 'reports',
                        'path'   => 'reports',
                        'link'   => 'index.php?option=com_volunteers&view=reports',
                        'params' => '{"menu-anchor_title":"","menu-anchor_css":"","menu_icon_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","robots":""}');
                    $menuitems[] = array(
                        'title'  => 'Help Wanted',
                        'alias'  => 'help-wanted',
                        'path'   => 'help-wanted',
                        'link'   => 'index.php?option=com_volunteers&view=roles',
                        'params' => '{"menu-anchor_title":"","menu-anchor_css":"","menu_icon_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","robots":""}');
                    $menuitems[] = array(
                        'title'  => 'FAQs',
                        'alias'  => 'faqs',
                        'path'   => 'faqs',
                        'link'   => 'index.php?option=com_content&view=category&id=0',
                        'params' => '{"show_category_title":"","show_description":"","show_description_image":"","maxLevel":"","show_empty_categories":"","show_no_articles":"","show_category_heading_title_text":"","show_subcat_desc":"","show_cat_num_articles":"","show_cat_tags":"","show_pagination_limit":"","filter_field":"","show_headings":"","list_show_date":"","date_format":"","list_show_hits":"","list_show_author":"","orderby_pri":"","orderby_sec":"","order_date":"","show_pagination":"","show_pagination_results":"","display_num":"","show_featured":"","article_layout":"_:default","show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_readmore":"","show_readmore_title":"","show_hits":"","show_noauth":"","show_feed_link":"","feed_summary":"","menu-anchor_title":"","menu-anchor_css":"","menu_icon_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","robots":""}');
                    $menuitems[] = array(
                        'title'  => 'Register',
                        'alias'  => 'register',
                        'path'   => 'register',
                        'link'   => 'index.php?option=com_volunteers&view=registration',
                        'params' => '{"menu-anchor_title":"","menu-anchor_css":"","menu_icon_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","robots":""}');
                    $menuitems[] = array(
                        'title'  => 'Login',
                        'alias'  => 'login',
                        'path'   => 'login',
                        'link'   => 'index.php?option=com_users&view=login',
                        'params' => '{"loginredirectchoice":"1","login_redirect_url":"","login_redirect_menuitem":"","logindescription_show":"1","login_description":"","login_image":"","login_image_alt":"","logoutredirectchoice":"1","logout_redirect_url":"","logout_redirect_menuitem":"","logoutdescription_show":"1","logout_description":"","logout_image":"","logout_image_alt":"","menu-anchor_title":"","menu-anchor_css":"","menu_icon_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","robots":""}');
                    foreach ($menuitems as $m) {
                        $mi = new MenuTable($db);
                        $mi->menutype     = 'jvpdemo';
                        $mi->title        = htmlspecialchars_decode($m['title']);
                        $mi->alias        = $m['alias'] . '-jvp';
                        $mi->path         = $m['path'];
                        $mi->link         = $m['link'];
                        $mi->params       = $m['params'];
                        $mi->type         = "component";
                        $mi->published    = 1;
                        $mi->parent_id    = 1;
                        $mi->client_id    = 0;
                        $mi->level        = 1;
                        $mi->component_id = $extension_id;
                        $mi->setLocation(1, 'last-child');
                        $mi->img      = '';
                        $mi->language = "*";
                        $mi->note     = '';
                        $mi->browserNav = 0;
                        $mi->template_style_id = 0;
                        $mi->home = 0;
                        if ($mi->store()) {
                            echo "Successfully Created Menu Item - " . $m['title'] . '<br/>' . $mi->getError() . '<br/>';
                        } else {
                            echo "Failed to create Menu Item - " . $m['title'] . '<br/>' . $mi->getError() . '<br/>';
                        }
                    }
                } catch (Exception $e) {
                    throw new Exception('Something went really wrong. ' . $e->getMessage(), 500);
                }
            }

            ?>

        </div>
    </div>
</div>
