# joomla-volunteer-portal
Based on the live Joomla 3 version code base ported to Joomla 4 and updated and now ported to Joomla 5. It does not need the backwards compatibility plugin.
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
Instructions for installation
------------------------------
* Create new installation of Joomla 5.

* Install Joomla 4  template from the dist folder (jtemplate_4.0.7.zip).
* Go to site System-> Templates-> Site Template Styles - enable Joomla-default as default template style.
* Install the volunteers package from dist/pkg-volunteers-4.0.0.zip
* Enable the plugin 'Sample Data - JVP'.
* On the Home Dashboard, you will see JVP Sample Data under the Sample Data heading. Click to install the sample data for the Volunteers Portal (This takes some time...) (This will log you out when you next try and do anything in the admin area - This is due to it moving your admin user to a safe id).
* Log back in and Click on Components -> Joomla Volunteers -> Set up Demo Menu and then Click the button GO (this will create a front end menu).
* Go to System -> Site Modules and click to edit 'Main Menu'.
* From the Select Menu dropdown choose 'Joomla Volunteer Portal Demo Menu',  enter 'Navigation [position-1]' in the position box, click on 'Title Show' and change to 'Title Hide'. Click on Advanced Tab and type 'nav menu nav-pills mod-list' in Menu Class . Then hit save and close.
* Whilst in the list of modules, edit and publish 'Module - Volunteers - Display Latest Joomlers' and 'Module - Display a Joomlers Story' setting both to 'Right [position-7]', and Menu Assignment to 'Only on the pages selected' and 'Joomla Volunteer Portal Demo Menu -> Home'.
* Go to System -> Plugins and search for volunteer. This will show the three Volunteer plugins. Click on `Status` to publish them.
* Finally click into Menus and Joomla Volunteer Portal Demo and open the Login entry. Then hit save and close. For some reason this one does not like being installed through code but opening and saving seems to fix it. And then click on the Home entry in the list and make sure it's set to be the default Home for the site.

------------------------------
Instructions for uninstalling
-----------------------------
* In Extensions Manage, just click to uninstall pkg_volunteers and this will uninstall all of the subcomponents.

------------------------------
Awaiting functionality
-----------------------------

* Adding new users/editing departments needs further testing.

---------------------------------------------------------------
Roll Call

* Original Volunteer Portal by [Sander Potjer](https://github.com/sanderpotjer) and [Roland Dalmulder](https://github.com/roland-d)
* Joomla 4 Port by [Mark Fleeson](https://github.com/mfleeson) and [Roland Dalmulder](https://github.com/roland-d)
* Code Clean Up, adding drone and jorobo by [Hannes Papenberg](https://github.com/hackwar)
* Code Clean Up and improvements by [Brian Teeman](https://github.com/brianteeman)
* Code port to Joomla 5 and move from Google Maps to OpenStreetMap by [Mark Fleeson](https://github.com/mfleeson)


