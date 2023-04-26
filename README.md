# joomla-volunteer-portal
Based on the live Joomla 3 version code base ported to Joomla 4 and updated.
---------------------------------------------------------------
Instructions for installation
------------------------------
* Create new installation of Joomla 4
* Download and install Joomla 3 template from https://github.com/joomla/jdotorgtemplate/releases/tag/3.1.1
* Go to site template styles - enable Joomla-default as default template style
* Download latest 4.0.1 Joomla Template from https://github.com/joomla/jdotorgtemplate/releases/tag/4.0.1 and install it.
* Install com_volunteers (this takes a while to process as it imports the sql data)
* Click on Components -> Joomla Volunteers -> Set up Demo Menu and then Click the button GO (this will create a front end menu)
* Go to System -> Site Modules 
* Create a new module of type Menu, title=JVP and select Joomla Volunteer Portal Demo Menu as the Select Menu. and position-1 

Not sure why menu is not appearing in white. It does in my test environment but not in a clean install.

---------------------------------------------------------------
Roll Call

*Original Volunteer Portal by Sander Potjer and Roland Dalmulder

*Joomla 4 Port by Mark Fleeson and Roland Dalmulder
