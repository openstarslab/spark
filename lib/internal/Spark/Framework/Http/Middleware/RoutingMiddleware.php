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

namespace Spark\Framework\Http\Middleware;

use Nulldark\Routing\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spark\Framework\Routing\RouteContext;

class RoutingMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected RouterInterface $router
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle(
            $this->findRoute($request),
        );
    }

    /**
     * Finds and sets the route attribute on the given server request.
     *
     * @param ServerRequestInterface $request
     *  $request The server request to find the route for.
     *
     * @return ServerRequestInterface
     *  The modified server request with the route attribute set.
     *
     * @throws \Nulldark\Routing\Exception\MethodNotAllowedException
     * @throws \Nulldark\Routing\Exception\RouteNotFoundException
     */
    public function findRoute(ServerRequestInterface $request): ServerRequestInterface
    {
        $request->withAttribute(RouteContext::ROUTE_FOUND->value, true);

        return $request->withAttribute(
            RouteContext::ROUTE->value,
            $this->router->match($request),
        );
    }
}
