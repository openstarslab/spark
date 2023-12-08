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

namespace Spark\Framework\DependencyInjection\Builder;

interface ContainerBuilderInterface
{
    /**
     * Sets a definition for a given identifier.
     *
     * @param string $id
     *  The identifier of the definition.
     * @param Definition $definition
     *  The definition to be set.
     *
     * @return Definition The new definition that was set.
     */
    public function setDefinition(string $id, Definition $definition): Definition;

    /**
     * Retrieves the definition for a specified id.
     *
     * @param string $id
     *  The id of the definition.
     *
     * @return Definition
     *  The corresponding definition object.
     */
    public function getDefinition(string $id): Definition;

    /**
     * Retrieves all definitions in reverse order.
     *
     * @return Definition[]
     *  An array of definitions in reverse order.
     */
    public function getDefinitions(): array;

    /**
     * Registers a new service to the container.
     *
     * @param string $id
     *  The identifier for the service.
     * @param string $class
     *  The fully-qualified class name of the service.
     *
     * @return Definition The registered service.
     */
    public function register(string $id, string $class): Definition;

    /**
     * Builds a container instance with service definitions.
     *
     * @return \Psr\Container\ContainerInterface
     *  The built container instance.
     */
    public function build(): \Psr\Container\ContainerInterface;

    /**
     * Retrieves the value associated with the given id.
     *
     * @param string $id
     *  The identifier for the value to retrieve.
     *
     * @return mixed
     *  The value associated with the given id.
     */
    public function get(string $id): mixed;

    /**
     * Checks if the given id exists in the container builder.
     *
     * @param string $id
     *  The identifier to check.
     *
     * @return bool
     *  Returns true if the given id exists, false otherwise.
     */
    public function has(string $id): bool;

    /**
     * Sets the value associated with the given id.
     *
     * @param string $id
     *  The identifier for the value to set.
     * @param mixed $value
     *  The value to set.
     *
     * @return void
     */
    public function set(string $id, mixed $value): void;
}