# joomla-volunteer-portal
Based on the live Joomla 3 version code base ported to Joomla 4 and updated.
---------------------------------------------------------------
Build Status
---------------------
| Drone-CI                                                                                                                                                                  |  PHP           |
|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------|  ------------- |
| [![Build Status](http://ci.joomla.org/api/badges/joomla-projects/joomla-volunteer-portal/status.svg)](http://ci.joomla.org/joomla-projects/joomla-volunteer-portal) | [![PHP](https://img.shields.io/badge/PHP-V8.1.0-green)](https://www.php.net/) |
------------------------------
Instructions for installation
------------------------------
* Create new installation of Joomla 4
* Download and install Joomla 3 template from https://github.com/joomla/jdotorgtemplate/releases/tag/3.1.1 or you can find it in the templates folder as jtemplate_3.1.1.zip in this repository
* Go to site template styles - enable Joomla-default as default template style
* Download latest 4.0.1 Joomla Template from https://github.com/joomla/jdotorgtemplate/releases/tag/4.0.1 or you can find it in the templates folder as jtemplate_4.0.1-jvp and install it.
* If you work from the repository, do a `composer install` and afterwards `vendor/bin/robo build` to build the package
* Install the volunteers package
* Enable the different plugins and modules belonging to the package, especially the sampledata plugin
* Install the sample data for the Volunteers Portal (This takes some time...)
* Click on Components -> Joomla Volunteers -> Set up Demo Menu and then Click the button GO (this will create a front end menu)
* Go to System -> Site Modules
* Create a new module of type Menu, title=JVP and select Joomla Volunteer Portal Demo Menu as the Select Menu. and position-1


---------------------------------------------------------------
Roll Call

* Original Volunteer Portal by [Sander Potjer](https://github.com/sanderpotjer) and [Roland Dalmulder](https://github.com/roland-d)
* Joomla 4 Port by [Mark Fleeson](https://github.com/mfleeson) and [Roland Dalmulder](https://github.com/roland-d)
* Code Clean Up, adding drone and jorobo by [Hannes Papenberg](https://github.com/hackwar)
* Code Clean Up and improvements by [Brian Teeman](https://github.com/brianteeman)
