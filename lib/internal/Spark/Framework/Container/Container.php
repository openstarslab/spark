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

use Spark\Framework\Container\Exception\ServiceCircularDependencyException;
use Spark\Framework\Container\Exception\ServiceNotFoundException;

/**
 * @template T
 */
class Container implements ContainerInterface
{
    /** @var array<string, T> $values */
    private array $values = [];

    /** @var array<string, callable> $factories */
    private array $factories = [];

    /**
     * @param array<string, string|T> $parameters
     */
    public function __construct(array $parameters = [])
    {
        foreach ($parameters as $key => $parameter) {
            $this->set($key, $parameter);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $id, mixed $value): void
    {
        $this->values[$id] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id): mixed
    {
        try {
            return $this->resolve($id);
        } catch (\Exception $e) {
            if ($this->has($id) || $e instanceof ServiceCircularDependencyException) {
                throw $e;
            }

            throw new ServiceNotFoundException($id);
        }
    }

    /**
     * @param string $id
     * @return ($id is class-string<T> ? T : mixed)
     */
    private function resolve(string $id): mixed
    {
        if (isset($this->values[$id])) {
            return $this->values[$id];
        }

        if (isset($this->factories[$id])) {
            $this->values[$id] = $this->factories[$id]($this);
        }

        return $this->values[$id];
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        return \array_key_exists($id, $this->values)
            || \array_key_exists($id, $this->factories);
    }

    /**
     * {@inheritDoc}
     */
    public function factory(string $id, callable $callable): void
    {
        $this->factories[$id] = $callable;
    }

    /**
     * @inheritDoc
     */
    public function register(ServiceProviderInterface $provider): self
    {
        $provider->register($this);

        return $this;
    }
}
