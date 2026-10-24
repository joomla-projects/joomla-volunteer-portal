# joomla-volunteer-portal
Based on the live Joomla 3 version code base ported to Joomla 4 and updated to Joomla 5 and now ported to Joomla 6. It does not need the backwards compatibility plugin.
---------------------------------------------------------------
Build Status
---------------------
| Drone-CI                                                                                                                                                                  |  PHP           |
|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------|  ------------- |
| [![Build Status](http://ci.joomla.org/api/badges/joomla-projects/joomla-volunteer-portal/status.svg)](http://ci.joomla.org/joomla-projects/joomla-volunteer-portal) | [![PHP](https://img.shields.io/badge/PHP-V8.1.0-green)](https://www.php.net/) |
---
PLEASE DO TEST. See below for missing functionality
---
Building on Windows
--
**Make sure you have php and composer installed**

In a command window run
* Clean-Windows.bat
* Build-Windows-a.bat
* Build-Windows-b.bat
* Build-Windows-c.bat

Building on Linux
--
In shell run
* sh clean-linux.sh
* sh build-linux.sh

------------------------------
Instructions for installation with Cassiopeia
------------------------------
* Create new installation of Joomla 6.
* Install the volunteers package from dist/pkg-volunteers-4.0.0.zip
* Install the test data seeder from dist/plg_console_volunteers.zip and enable the plugin 'Console - Joomla Volunteer Portal'.
* Run `php cli/joomla.php volunteers:seed` to add the test data, see [Test data](#test-data). This moves your admin user to a free ID below 30, so log in again in the admin area.
* Click on Components -> Joomla Volunteers -> Set up Demo Menu and then Click the button GO (this will create a front end menu).
* Go to System -> Site Modules and click to edit 'Main Menu'.
* From the Select Menu dropdown choose 'Joomla Volunteer Portal Demo Menu',  enter 'menu' in the position box, click on 'Title Show' and change to 'Title Hide'. Then hit save and close.
* Whilst in the list of modules, edit and publish 'Module - Volunteers - Display Latest Joomlers' and 'Module - Display a Joomlers Story' setting both to 'Right [sidebar-right]', and Menu Assignment to 'Only on the pages selected' and 'Joomla Volunteer Portal Demo Menu -> Home'.
* Go to System -> Plugins and search for volunteer. This will show the three Volunteer plugins. Click on `Status` to publish them.
* Finally click into Menus and Joomla Volunteer Portal Demo and open the Login entry. Then hit save and close. For some reason this one does not like being installed through code but opening and saving seems to fix it. And then click on the Home entry in the list and make sure it's set to be the default Home for the site.
  
------------------------------
Instructions for installation with Joomla Common Site Template
------------------------------
* Create new installation of Joomla 6.

* Install Joomla 4  template from the dist folder (jtemplate_4.0.7.zip).
* Go to site System-> Templates-> Site Template Styles - enable Joomla-default as default template style.
* Install the volunteers package from dist/pkg-volunteers-4.0.0.zip
* Install the test data seeder from dist/plg_console_volunteers.zip and enable the plugin 'Console - Joomla Volunteer Portal'.
* Run `php cli/joomla.php volunteers:seed` to add the test data, see [Test data](#test-data). This moves your admin user to a free ID below 30, so log in again in the admin area.
* Click on Components -> Joomla Volunteers -> Set up Demo Menu and then Click the button GO (this will create a front end menu).
* Go to System -> Site Modules and click to edit 'Main Menu'.
* From the Select Menu dropdown choose 'Joomla Volunteer Portal Demo Menu',  enter 'Navigation [position-1]' in the position box, click on 'Title Show' and change to 'Title Hide'. Click on Advanced Tab and type 'nav menu nav-pills mod-list' in Menu Class. Then hit save and close.
* Whilst in the list of modules, edit and publish 'Module - Volunteers - Display Latest Joomlers' and 'Module - Display a Joomlers Story' setting both to 'Right [position-7]', and Menu Assignment to 'Only on the pages selected' and 'Joomla Volunteer Portal Demo Menu -> Home'.
* Go to System -> Plugins and search for volunteer. This will show the three Volunteer plugins. Click on `Status` to publish them.
* Finally click into Menus and Joomla Volunteer Portal Demo and open the Login entry. Then hit save and close. For some reason this one does not like being installed through code but opening and saving seems to fix it. And then click on the Home entry in the list and make sure it's set to be the default Home for the site.

------------------------------
Test data
------------------------------
The development plugin in `dev/plugins/console/volunteers` adds the `volunteers:seed` command to the Joomla CLI. It fills an empty portal with 2186 test users (with an e-mail address at dummy.com), departments, positions, roles, teams, Joomlers and reports from the SQL files in its `sql` folder. It is not part of the package, `build-linux.sh` builds it as dist/plg_console_volunteers.zip. Never install it on a live site.

* `php cli/joomla.php volunteers:seed` adds the test data after a confirmation, it stops when the volunteers tables already contain data.
* `--force` removes the existing volunteers data and the earlier test users first.
* `--skip-reports` leaves out the reports, the largest part of the test data.
* A Super User with the ID of a test user is moved to a free ID below 30.
* Run `php cli/joomla.php finder:index` afterwards to find the test data with Smart Search.

------------------------------
Instructions for uninstalling
-----------------------------
* In Extensions Manage, just click to uninstall pkg_volunteers and this will uninstall all of the subcomponents.

------------------------------
Awaiting functionality
-----------------------------
* Awaiting links to Joomla Identity Site

---------------------------------------------------------------
Roll Call

* Original Volunteer Portal by [Sander Potjer](https://github.com/sanderpotjer) and [Roland Dalmulder](https://github.com/roland-d)
* Joomla 4 Port by [Mark Fleeson](https://github.com/mfleeson) and [Roland Dalmulder](https://github.com/roland-d)
* Code Clean Up, adding drone and jorobo by [Hannes Papenberg](https://github.com/hackwar)
* Code Clean Up and improvements by [Brian Teeman](https://github.com/brianteeman)
* Code port to Joomla 5 and Joomla 6 and move from Google Maps to OpenStreetMap by [Mark Fleeson](https://github.com/mfleeson)


