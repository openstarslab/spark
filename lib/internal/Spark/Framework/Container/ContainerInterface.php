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

namespace Spark\Framework\Container;

interface ContainerInterface extends \Psr\Container\ContainerInterface
{
    /**
     * Sets a service with the provided ID.
     *
     * @param string $id
     *  The ID of the service to set.
     * @param mixed $value
     *  The service to set.
     *
     * @return void
     */
    public function set(string $id, mixed $value): void;

    /**
     * Retrieves a service based on the provided ID.
     *
     * @param string $id
     *  The ID of the service to retrieve.
     *
     * @return mixed
     *  The retrieved service.
     *
     * @throws \Spark\Framework\Container\Exception\ServiceNotFoundException
     * @throws \Spark\Framework\Container\Exception\ServiceCircularDependencyException
     */
    public function get(string $id): mixed;

    /**
     * Creates and registers a service factory.
     *
     * @param string $id
     *  The ID for the service factory.
     * @param callable $callable
     *  A callable function that returns the service instance.
     *
     * @return void
     */
    public function factory(string $id, callable $callable): void;

    /**
     * Registers a service provider to the container.
     *
     * @param ServiceProviderInterface $provider
     *     The service provider to be registered.
     *
     * @return self
     *     The container instance.
     */
    public function register(ServiceProviderInterface $provider): self;
}
