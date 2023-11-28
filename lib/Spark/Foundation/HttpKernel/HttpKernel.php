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

namespace Spark\Foundation\HttpKernel;

use Nulldark\Container\ContainerInterface;
use Nulldark\Routing\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spark\Routing\RouteRunner;

class HttpKernel implements HttpKernelInterface
{
    protected RouteRunner $routeRunner;

    public function __construct(
        protected ContainerInterface $container,
        protected RouterInterface $router
    ) {
        $this->routeRunner = $this->container->get('route_runner');
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handleRequest($request);
    }

    protected function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        return $this->routeRunner->run(
            $this->router->match($request)
        );
    }
}