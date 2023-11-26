<?php

/**
 * Copyright (C) 2023 OpenStars Lab Development Team
 *
 * This file is part of spark/spark
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Spark\Core\Foundation\Providers;

use Nulldark\Container\ContainerInterface;
use Nulldark\Routing\Router;
use Nyholm\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Spark\Core\Foundation\HttpKernel\HttpKernel;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerHttpKernel();
        $this->registerCoreAliases();
    }

    /**
     * Registers a http kernel.
     *
     * @return void
     */
    private function registerHttpKernel(): void
    {
        $this->container->bind('http_kernel', function (ContainerInterface $container) {
            return new HttpKernel(
                $container,
                $container->get('router')
            );
        });
    }

    /**
     * Registers a core aliases.
     *
     * @return void
     */
    private function registerCoreAliases(): void
    {
        foreach ([
            'request' => [Request::class, RequestInterface::class],
            'router' => [Router::class]
        ] as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->container->alias($key, $alias);
            }
        }
    }
}