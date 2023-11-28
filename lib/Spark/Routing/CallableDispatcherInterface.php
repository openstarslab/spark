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

use Nulldark\Routing\Route;
use Psr\Http\Message\ResponseInterface;

interface CallableDispatcherInterface
{
    /**
     * Dispatchs and returns prepared response.
     *
     * @param Route $route
     *  Matched route.
     * @param callable $callable
     *  Prepared callable to dispatch.
     *
     * @return ResponseInterface
     *  Returns a response.
     *
     */
    public function dispatch(Route $route, callable $callable): ResponseInterface;
}