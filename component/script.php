<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

define('MODIFIED', 1);
define('NOT_MODIFIED', 2);

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerScript;

/**
 * Allows any modifications to installation
 *
 * @since    4.0.0
 */
class com_volunteersInstallerScript extends InstallerScript
{
    /**
     * The title of the component (printed on installation and uninstallation messages)
     *
     * @var string
     *
     * @since 4.0.0
     */
    protected $extension = 'Joomla Volunteers Portal';

    /**
     * The minimum Joomla! version required to install this extension
     *
     * @var   string
     *
     * @since 4.0.0
     */
    protected $minimumJoomla = '4.0';

    /**
     * Method to install the component
     *
     * @param   mixed  $parent  Object who called this method.
     *
     * @return void
     *
     * @since 0.2b
     * @throws Exception
     */
    public function install($parent)
    {
        $this->installLibrary($parent);
        $this->installPlugins($parent);
        $this->installModules($parent);
    }

    /**
     * Installs modules for this component
     *
     * @param   mixed  $parent  Object who called the install/update method
     *
     * @return void
     *
     * @since 4.0.0
     * @throws Exception
     */
    private function installLibrary($parent)
    {
        $installation_folder = $parent->getParent()->getPath('source');
        $app                 = Factory::getApplication();

        if (method_exists($parent, 'getManifest')) {
            $man = $parent->getManifest();
        } else {
            $man = $parent->get('manifest');
        }


        if (method_exists($parent, 'getManifest')) {
            $libraries = $parent->getManifest()->libraries;
        } else {
            $libraries = $parent->get('manifest')->libraries;
        }

        if (!empty($libraries)) {
            if (count($libraries->children())) {
                foreach ($libraries->children() as $library) {
                    $libraryName = (string) $library['library'];
                    $path        = $installation_folder . '/libraries/' . $libraryName;
                    $destpath    = (string) JPATH_SITE . '/libraries/' . $libraryName;
                    try {
                        $directory = opendir($path);
                        if (is_dir($destpath) === false) {
                            mkdir($destpath);
                        }
                        while (($file = readdir($directory)) !== false) {
                            if ($file === '.' || $file === '..') {
                                continue;
                            }


                            copy("$path/$file", "$destpath/$file");
                        }

                        closedir($directory);
                    } catch (Exception $e) {
                        $app->enqueueMessage('Library ' . $libraryName . ' was not installed successfully. - ' . $e->getMessage());

                        return;
                    }
                    $app->enqueueMessage('Library ' . $libraryName . ' was installed successfully');

                    return;
                }
            }
        }
    }

    /**
     * Installs plugins for this component
     *
     * @param   mixed  $parent  Object who called the install/update method
     *
     * @return void
     *
     * @since 4.0.0
     * @throws Exception
     */
    private function installPlugins($parent)
    {
        $installation_folder = $parent->getParent()->getPath('source');
        $app                 = Factory::getApplication();

        /* @var $plugins SimpleXMLElement */
        if (method_exists($parent, 'getManifest')) {
            $plugins = $parent->getManifest()->plugins;
        } else {
            $plugins = $parent->get('manifest')->plugins;
        }

        if (count($plugins->children())) {
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->getQuery(true);

            foreach ($plugins->children() as $plugin) {
                $pluginName  = (string) $plugin['plugin'];
                $pluginGroup = (string) $plugin['group'];
                $path        = $installation_folder . '/plugins/' . $pluginGroup . '/' . $pluginName;
                $installer   = new Installer();

                if (!$this->isAlreadyInstalled('plugin', $pluginName, $pluginGroup)) {
                    $result = $installer->install($path);
                } else {
                    $result = $installer->update($path);
                }

                if ($result) {
                    $app->enqueueMessage('Plugin ' . $pluginName . ' was installed successfully');
                } else {
                    $app->enqueueMessage(
                        'There was an issue installing the plugin ' . $pluginName,
                        'error'
                    );
                }

                $query
                    ->clear()
                    ->update('#__extensions')
                    ->set('enabled = 1')
                    ->where(
                        array(
                            'type LIKE ' . $db->quote('plugin'),
                            'element LIKE ' . $db->quote($pluginName),
                            'folder LIKE ' . $db->quote($pluginGroup)
                        )
                    );
                $db->setQuery($query);
                $db->execute();
            }
        }
    }

    /**
     * Installs modules for this component
     *
     * @param   mixed  $parent  Object who called the install/update method
     *
     * @return void
     *
     * @since 4.0.0
     * @throws Exception
     */
    private function installModules($parent)
    {
        $installation_folder = $parent->getParent()->getPath('source');
        $app                 = Factory::getApplication();

        if (method_exists($parent, 'getManifest')) {
            $modules = $parent->getManifest()->modules;
        } else {
            $modules = $parent->get('manifest')->modules;
        }

        if (!empty($modules)) {
            if (count($modules->children())) {
                foreach ($modules->children() as $module) {
                    $moduleName = (string) $module['module'];
                    $path       = $installation_folder . '/modules/' . $moduleName;
                    $installer  = new Installer();

                    if (!$this->isAlreadyInstalled('module', $moduleName)) {
                        $result = $installer->install($path);
                    } else {
                        $result = $installer->update($path);
                    }

                    if ($result) {
                        $app->enqueueMessage('Module ' . $moduleName . ' was installed successfully');
                    } else {
                        $app->enqueueMessage(
                            'There was an issue installing the module ' . $moduleName,
                            'error'
                        );
                    }
                }
            }
        }
    }

    /**
     * Check if an extension is already installed in the system
     *
     * @param   string  $type    Extension type
     * @param   string  $name    Extension name
     * @param   mixed   $folder  Extension folder(for plugins)
     *
     * @return boolean
     *
     * @since 4.0.0
     */
    private function isAlreadyInstalled($type, $name, $folder = null)
    {
        $result = false;

        switch ($type) {
            case 'plugin':
                $result = file_exists(JPATH_PLUGINS . '/' . $folder . '/' . $name);
                break;
            case 'module':
                $result = file_exists(JPATH_SITE . '/modules/' . $name);
                break;
            case 'library':
                $result = file_exists(JPATH_SITE . '/libraries/' . $name);
                break;
        }

        return $result;
    }

    /**
     * @param   string  $type    type
     * @param   string  $parent  parent
     *
     * @return boolean
     * @since 4.0.0
     */
    public function postflight($type, $parent)
    {


        return true;
    }

    /**
     * Method called before install/update the component. Note: This method won't be called during uninstall process.
     *
     * @param   string  $type    Type of process [install | update]
     * @param   mixed   $parent  Object who called this method
     *
     * @return boolean True if the process should continue, false otherwise
     * @since 4.0.0
     * @throws Exception
     *
     */
    public function preflight($type, $parent): bool
    {
        $result = parent::preflight($type, $parent);

        if (!$result) {
            return $result;
        }

        // logic for preflight before install
        return $result;
    }

    /**
     * Method to uninstall the component
     *
     * @param   mixed  $parent  Object who called this method.
     *
     * @return void
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function uninstall($parent)
    {
        $this->uninstallPlugins($parent);
        $this->uninstallModules($parent);
        $this->uninstallLibraries($parent);
    }

    /**
     * Uninstalls plugins
     *
     * @param   mixed  $parent  Object who called the uninstall method
     *
     * @return void
     *
     * @since 4.0.0
     * @throws Exception
     */
    private function uninstallPlugins($parent)
    {
        $app = Factory::getApplication();

        if (method_exists($parent, 'getManifest')) {
            $plugins = $parent->getManifest()->plugins;
        } else {
            $plugins = $parent->get('manifest')->plugins;
        }

        if (count($plugins->children())) {
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->getQuery(true);

            foreach ($plugins->children() as $plugin) {
                $pluginName  = (string) $plugin['plugin'];
                $pluginGroup = (string) $plugin['group'];
                $query
                    ->clear()
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where(
                        array(
                            'type LIKE ' . $db->quote('plugin'),
                            'element LIKE ' . $db->quote($pluginName),
                            'folder LIKE ' . $db->quote($pluginGroup)
                        )
                    );
                $db->setQuery($query);
                $extension = $db->loadResult();

                if (!empty($extension)) {
                    $installer = new Installer();
                    $result    = $installer->uninstall('plugin', $extension);

                    if ($result) {
                        $app->enqueueMessage('Plugin ' . $pluginName . ' was uninstalled successfully');
                    } else {
                        $app->enqueueMessage(
                            'There was an issue uninstalling the plugin ' . $pluginName,
                            'error'
                        );
                    }
                }
            }
        }
    }

    /**
     * Uninstalls modules
     *
     * @param   mixed  $parent  Object who called the uninstall method
     *
     * @return void
     *
     * @since 4.0.0
     * @throws Exception
     */
    private function uninstallModules($parent)
    {
        $app = Factory::getApplication();

        if (method_exists($parent, 'getManifest')) {
            $modules = $parent->getManifest()->modules;
        } else {
            $modules = $parent->get('manifest')->modules;
        }

        if (!empty($modules)) {
            if (count($modules->children())) {
                $db    = Factory::getContainer()->get('DatabaseDriver');
                $query = $db->getQuery(true);

                foreach ($modules->children() as $plugin) {
                    $moduleName = (string) $plugin['module'];
                    $query
                        ->clear()
                        ->select('extension_id')
                        ->from('#__extensions')
                        ->where(
                            array(
                                'type LIKE ' . $db->quote('module'),
                                'element LIKE ' . $db->quote($moduleName)
                            )
                        );
                    $db->setQuery($query);
                    $extension = $db->loadResult();

                    if (!empty($extension)) {
                        $installer = new Installer();
                        $result    = $installer->uninstall('module', $extension);

                        if ($result) {
                            $app->enqueueMessage('Module ' . $moduleName . ' was uninstalled successfully');
                        } else {
                            $app->enqueueMessage(
                                'There was an issue uninstalling the module ' . $moduleName,
                                'error'
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Uninstalls plugins
     *
     * @param   mixed  $parent  Object who called the uninstall method
     *
     * @return void
     *
     * @since 4.0.0
     * @throws Exception
     */
    private function uninstallLibraries($parent)
    {
        $app = Factory::getApplication();

        if (method_exists($parent, 'getManifest')) {
            $libraries = $parent->getManifest()->libraries;
        } else {
            $libraries = $parent->get('manifest')->libraries;
        }

        if (!empty($libraries)) {
            if (count($libraries->children())) {
                foreach ($libraries->children() as $library) {
                    $libraryName = (string) $library['library'];
                    $destpath    = JPATH_SITE . '/libraries/' . $libraryName;
                    try {
                        if (is_dir($destpath) === false) {
                            array_map("unlink", glob("*.*"));
                            rmdir($destpath);
                        }
                    } catch (Exception $e) {
                        $app->enqueueMessage('There was an issue uninstalling the library ' . $libraryName . ' - ' . $e->getMessage());

                        return;
                    }
                    $app->enqueueMessage('Library ' . $libraryName . ' was uninstalled successfully');

                    return;
                }
            }
        }
    }

    /**
     * Method to update the component
     *
     * @param   mixed  $parent  Object who called this method.
     *
     * @return void
     *
     * @since 4.0.0
     * @throws Exception
     */
    public function update($parent)
    {

        $this->installLibrary($parent);
        $this->installPlugins($parent);
        $this->installModules($parent);
    }
}
