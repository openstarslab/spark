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

namespace Spark\Core\Routing;

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Nulldark\Routing\Route;
use Psr\Http\Message\ResponseInterface;
use Spark\Core\DependencyInjection\ContainerAwareInterface;
use Spark\Core\DependencyInjection\ContainerAwareTrait;

class RouteRunner implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    public function __construct(
        protected CallableResolver $resolver,
        protected CallableDispatcher $dispatcher,
    ) {
    }

    /**
     * Resolves and emits a response.
     *
     * @param Route $route
     *  The route
     *
     * @return ResponseInterface
     *  Returns emitted response.
     */
    public function run(Route $route): ResponseInterface
    {
        $resolve = $this->resolver->resolve($route);
        $response = $this->dispatcher->dispatch($route, $resolve);

        $emitter = new SapiEmitter();
        $emitter->emit($response);

        return $response;
    }
}