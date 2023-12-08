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

namespace Spark\Framework\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spark\Framework\Http\Middleware\RoutingMiddleware;
use Spark\Framework\Http\Response;

final class RouteRunner implements RequestHandlerInterface
{
    public function __construct(
        protected CallableResolverInterface $callableResolver = new CallableResolver()
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getAttribute(RouteContext::ROUTE_FOUND->value) === null) {
            $routingMiddleware = new RoutingMiddleware();
            $request = $routingMiddleware->findRoute($request);
        }

        $callable = $this->callableResolver->resolve(
            $route = $request->getAttribute(RouteContext::ROUTE->value)
        );

        $response = new Response();
        $response = $callable($request, $response, ...$route->getParameters());

        if (!($response instanceof ResponseInterface)) {
            $msg = 'The controller must return a "\Psr\Http\Message\ResponseInterface" (%s given)' ;

            if ($response === null) {
                $msg .= ' Did you forget to add a return statement somewhere in your controller?';
            }

            throw new \RuntimeException(\sprintf($msg, \gettype($response)));
        }

        return $response;
    }
}
