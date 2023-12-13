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

interface ContainerInterface
{
    public const EXCEPTION_ON_INVALID_REFERENCE = 1;
    public const NULL_ON_INVALID_REFERENCE = 2;

    /**
     * Sets a service with the provided ID.
     *
     * @param string $id
     *  The ID of the service to set.
     * @param object $service
     *  The service to set.
     *
     * @return void
     */
    public function set(string $id, object $service): void;

    /**
     * Retrieves a service based on the provided ID.
     *
     * @template B of self::*_REFERENCE
     * @template T of object
     *
     * @param class-string<T> $id
     *  The ID of the service to retrieve.
     * @param B $behavior
     *  The way to behave if the container does not find a suitable service.
     *
     * @return (B is self::EXCEPTION_ON_INVALID_REFERENCE ? T : T|null)
     * @psalm-return (B is self::EXCEPTION_ON_INVALID_REFERENCE ? T : T|null)
     *  The retrieved service.
     *
     * @throws \Spark\Framework\Container\Exception\ServiceNotFoundException
     * @throws \Spark\Framework\Container\Exception\ServiceCircularDependencyException
     */
    public function get(string $id, int $behavior = self::NULL_ON_INVALID_REFERENCE): ?object;

    /**
     * Returns true if the container can return an entry for the given identifier, otherwise false.
     *
     * @param string $id
     *  Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool;

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

    /**
     * Retrieves the value of a parameter based on its name.
     *
     * @param string $name
     *   The name of the parameter whose value to retrieve.
     *
     * @return int|float|string|array
     *   The value of the parameter.
     *
     * @throws \InvalidArgumentException
     *   If the parameter name is empty.
     */
    public function getParameter(string $name): int|float|string|array;

    /**
     * Sets the value of a parameter.
     *
     * @param string $name
     *  The name of the parameter.
     * @param int|float|string|array $value
     *  The value of the parameter.
     *
     * @return void
     */
    public function setParameter(string $name, int|float|string|array $value): void;

    /**
     * Sets the values of multiple parameters.
     *
     * @param array<string, int|float|string> $parameters
     *  The array of parameters to set.
     *  The keys of the array are the names of the parameters, and the values are their corresponding values.
     *
     * @return void
     */
    public function setParameters(array $parameters): void;

    /**
     * Checks if a parameter exists.
     *
     * @param string $name
     *  The name of the parameter to check.
     *
     * @return bool
     *  Returns "true" if the parameter exists, otherwise "false".
     */
    public function hasParameter(string $name): bool;
}
