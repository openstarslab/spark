<?php

namespace Spark\Core\Foundation\Providers;

use Nulldark\Routing\Router;
use Nulldark\Routing\RouterInterface;

/**
 * Routing Service Provider
 *
 * @since   2023-11-18
 * @version 0.1.0-alpha
 * @package Spark\Core\Foundation\Providers
 * @author  Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license https://opensource.org/license/lgpl-2-1/
 * @link    https://github.com/openstarslab/spark-core
 */
class RoutingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RouterInterface::class, Router::class);
    }
}
