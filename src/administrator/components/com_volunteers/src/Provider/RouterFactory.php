<?php

/**
 * @package    com_volunteers
 *
 * @copyright  (C) 2023 Open Source Matters, Inc.  <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace  Joomla\Component\Volunteers\Administrator\Provider;

use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;

class RouterRouterFactory implements \Joomla\DI\ServiceProviderInterface
{
    /**
     * The component's namespace
     *
     * @var     string
     *
     * @since   4.0.0
     */
    private $namespace;

    /**
     * Router factory constructor.
     *
     * @param   string  $namespace  The namespace
     *
     * @since   4.0.0
     */
    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @inheritDoc
     *
     * @since 4.0.0
     */
    public function register(Container $container)
    {
        $container->set(
            RouterFactoryInterface::class,
            function (Container $container) {
                return new \Joomla\Component\Volunteers\Administrator\Service\RouterFactory(
                    $this->namespace,
                    $container->get(DatabaseInterface::class),
                    $container->get(MVCFactoryInterface::class),
                    $container->get(CategoryFactoryInterface::class)
                );
            }
        );
    }
}