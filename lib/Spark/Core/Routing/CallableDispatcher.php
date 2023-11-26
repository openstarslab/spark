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

use Nulldark\Container\ContainerInterface;
use Nulldark\Container\Exception\NotFoundException;
use Nulldark\Routing\Route;
use Psr\Http\Message\ResponseInterface;

class CallableDispatcher implements CallableDispatcherInterface
{
    public function __construct(
        protected ContainerInterface $container
    ) {}

    /**
     * @inheritDoc
     */
    public function dispatch(Route $route, callable $callable): ResponseInterface
    {
        return $this->container->call(
            $callable,
            $this->resolveParameters($route, new \ReflectionFunction($callable))
        );
    }

    private function resolveParameters(Route $route, \ReflectionFunctionAbstract $reflection): array
    {
        $params = [];
        $routeParams = $route->getParameters();

        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            if (\array_key_exists($parameter->getName(), $routeParams)) {
                $params[$name] = $routeParams[$parameter->getName()];
            } elseif ($type !== null && !$type->isBuiltin()) {
                try {
                    $params[$name] = $this->container->get($type->getName());
                } catch (NotFoundException $exc) {
                    if ($this->container->has($parameter->getName())) {
                        $params[$name] = $this->container->get($parameter->getName());
                    }

                    throw $exc;
                }
            } else {
                if ($parameter->isDefaultValueAvailable() && $parameter->isOptional()) {
                    $params[$name] = $parameter->getDefaultValue();
                }
            }
        }

        return $params;
    }
}