<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Console.volunteers
 *
 * @copyright   (C) 2026 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\Console\Volunteers\Extension;

use Joomla\Application\ApplicationEvents;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Joomla\Plugin\Console\Volunteers\Console\SeedCommand;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Adds the commands of the Joomla Volunteer Portal to the Joomla CLI
 *
 * @since  6.1.0
 */
final class Volunteers extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   6.1.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ApplicationEvents::BEFORE_EXECUTE => 'registerCommands',
        ];
    }

    /**
     * Register the commands of the Joomla Volunteer Portal.
     *
     * @return  void
     *
     * @since   6.1.0
     */
    public function registerCommands(): void
    {
        $command = new SeedCommand();
        $command->setDatabase($this->getDatabase());
        $command->setSqlPath(\dirname(__DIR__, 2) . '/sql');

        $this->getApplication()->addCommand($command);
    }
}
