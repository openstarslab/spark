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

namespace Spark\Framework\Extension;

use Spark\Framework\Container\ContainerAwareInterface;
use Spark\Framework\Container\ContainerInterface;

interface ExtensionInterface extends ContainerAwareInterface
{
    /**
     * Boot the application.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Registers a given extension into kernel.
     *
     * @param \Spark\Framework\Container\ContainerInterface $container
     *   The container instance.
     *
     * @return void
     */
    public function register(ContainerInterface $container): void;

    /**
     * Returns the name of the extension.
     *
     * @return string
     *    The name of the extension.
     */
    public function getName(): string;

    /**
     * Gets the path of the extension.
     *
     * @return string
     *   The path of the extension.
     */
    public function getPath(): string;

    /**
     * Check if the extension is active.
     *
     * @return bool
     *  `TRUE` if the extension is active, `FALSE` otherwise.
     */
    public function isActive(): bool;

    /**
     * Load routes using a callback function.
     *
     * @param \Closure $callback
     *  A callback function for defining routes.
     *
     * @return void
     */
    public function loadRoutes(\Closure $callback): void;
}
