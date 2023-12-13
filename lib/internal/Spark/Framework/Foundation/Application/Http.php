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

namespace Spark\Framework\Foundation\Application;

use Nulldark\Routing\RouterInterface;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spark\Framework\Container\ContainerInterface;
use Spark\Framework\Http\MiddlewareDispatcher;
use Spark\Framework\Routing\RouteRunner;

class Http implements ApplicationInterface
{
    protected MiddlewareDispatcher $dispatcher;

    public function __construct(
        protected ContainerInterface $container
    ) {
        $this->dispatcher = new MiddlewareDispatcher(
            new RouteRunner($container->get(RouterInterface::class)),
        );
    }

    /**
     * @inheritDoc
     */
    public function start(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->dispatcher->handle($request);

        if ($request->getMethod() === 'HEAD') {
            $response->withBody(Stream::create());
        }

        return $response;
    }
}
