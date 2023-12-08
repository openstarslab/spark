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

use Nulldark\Container\Container;
use Spark\Framework\DependencyInjection\Exception\ServiceCircularDependencyException;

class ContainerBuilder implements ContainerBuilderInterface
{
    /**
     * @var array<string, bool> $buildStack
     */
    private array $buildStack = [];

    /**
     * An array that stores the definitions of various items.
     *
     * @var array<string, Definition> $definitions
     */
    private array $definitions;

    /**
     * @var array $parameters
     */
    private array $storage = [];

    /**
     * {@inheritdoc}
     */
    public function setDefinition(string $id, Definition $definition): Definition
    {
        return $this->definitions[$id] = $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(string $id): Definition
    {
        return $this->definitions[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitions(): array
    {
        return \array_reverse($this->definitions);
    }

    /**
     * {@inheritdoc}
     */
    public function register(string $id, string $class): Definition
    {
        return $this->setDefinition($id, new Definition($class));
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $id, mixed $value): void
    {
        $this->storage[$id] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $id): bool
    {
        return \array_key_exists($id, $this->storage);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $id): mixed
    {
        return $this->storage[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): \Psr\Container\ContainerInterface
    {
        $container = new Container();

        foreach ($this->getDefinitions() as $id => $definition) {
            $container->bind($id, $this->createService($definition, $id));
        }

        return $container;
    }

    private function createService(Definition $definition, string $id): mixed
    {
        if (isset($this->buildStack[$id])) {
            throw new ServiceCircularDependencyException($id, \array_merge(\array_keys($this->buildStack), [$id]));
        }

        $this->buildStack[$id] = true;

        $class = $definition->getClass();
        $arguments = $definition->getArguments();


        $r = new \ReflectionClass($class);

        return $r->getConstructor()
            ? $r->newInstanceArgs($arguments)
            : $r->newInstance();
    }
}
