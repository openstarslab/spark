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

namespace Spark\Routing;

use Nulldark\Container\ContainerInterface;
use Nulldark\Routing\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spark\Foundation\Providers\ServiceProvider;

class RoutingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerRouter();
        $this->registerCallableResolver();
        $this->registerCallableDispatcher();
        $this->registerRouteRunner();
        $this->registerRequest();
        $this->registerResponse();
    }

    /**
     * Registers the router instance.
     *
     * @return void
     */
    private function registerRouter(): void
    {
        $this->container->singleton('router', new Router());
    }

    public function registerRouteRunner(): void
    {
        $this->container->singleton('route_runner', new RouteRunner(
            $this->container->get('callable_resolver'),
            $this->container->get('callable_dispatcher')
        ));
    }

    /**
     * Registers the callable resolver.
     *
     * @return void
     */
    private function registerCallableResolver(): void
    {
        $this->container->singleton('callable_resolver', function () {
            return new CallableResolver();
        });
    }

    /**
     * Registers the callable dispatcher
     *
     * @return void
     */
    private function registerCallableDispatcher(): void
    {
        $this->container->singleton('callable_dispatcher', function (ContainerInterface $container) {
            return new CallableDispatcher($container);
        });
    }

    /**
     * Registers PSR-7 request implementation.
     *
     * @return void
     */
    private function registerRequest(): void
    {
        $this->container->bind(ServerRequestInterface::class, function (ContainerInterface $container) {
            $psr17Factory = new Psr17Factory();
            $serverRequestCreator = new ServerRequestCreator(
                $psr17Factory,
                $psr17Factory,
                $psr17Factory,
                $psr17Factory
            );

            return $serverRequestCreator->fromGlobals();
        });
    }

    /**
     * Registers PSR-7 response implementation.
     *
     * @return void
     */
    private function registerResponse(): void
    {
        $this->container->bind(ResponseInterface::class, function () {
            return new Response();
        });
    }
}