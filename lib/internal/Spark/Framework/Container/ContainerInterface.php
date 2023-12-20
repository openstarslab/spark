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

/**
 * @property-read mixed $value
 */
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
     */
    public function get(string $id, int $behavior = self::EXCEPTION_ON_INVALID_REFERENCE): ?object;

    /**
     * Make a service of the container by its name.
     *
     * @template B of self::*_REFERENCE
     * @template T of object
     *
     * @param class-string<T> $id
     *  The ID of the object to make.
     * @param B $behavior
     *  The behavior when a service does not exist.
     *      - self::EXCEPTION_ON_INVALID_REFERENCE: Throw an exception on invalid reference.
     *      - self::NULL_ON_INVALID_REFERENCE: Returns a null on invalid reference
     *
     * @return (B is self::EXCEPTION_ON_INVALID_REFERENCE ? T : T|null)
     * @psalm-return (B is self::EXCEPTION_ON_INVALID_REFERENCE ? T : T|null)
     *  The created object or null if the behavior is set to EXCEPTION_ON_INVALID_REFERENCE.
     *
     * @throws \Spark\Framework\Container\Exception\ServiceNotFoundException
     */
    public function make(string $id, int $behavior = self::EXCEPTION_ON_INVALID_REFERENCE): ?object;

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
     * Binds an implementation to a specified identifier.
     *
     * The bind method allows you to associate an implementation with a specific identifier.
     * The identifier can then be used to retrieve the implementation through Dependency Injection or
     * Service Location.
     *
     * @param string $id
     *  The identifier to bind the implementation to.
     * @param callable|object $concrete
     *  The implementation to bind. This can be a callable or an object.
     * @param bool $singleton
     *  (Optional) Defines whether the bound implementation should be treated as a singleton. Default is false.
     *
     * @return void
     */
    public function bind(string $id, callable|object $concrete, bool $singleton = null): void;

    /**
     * Registers a shared service in the container.
     *
     * @param string $id
     *   The unique identifier for the service.
     * @param object $concrete
     *   A closure or class name that will be invoked to create the singleton instance.
     *
     * @return void
     */
    public function singleton(string $id, object $concrete): void;

    /**
     * Registers a service provider to the container.
     *
     * @param \Spark\Framework\Container\ServiceProvider $provider
     *     The service provider to be registered.
     *
     * @return \Spark\Framework\Container\ServiceProvider
     *     The container instance.
     */
    public function register(ServiceProvider $provider): ServiceProvider;

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
