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

class Container implements ContainerInterface
{
    /** @var array<string, object> $values */
    private array $values = [];

    /** @var array<string, callable> $factories */
    private array $factories = [];

    /** @var array<string, int|float|string|array> $parameters */
    private array $parameters = [];

    /**
     * @param array<string, int|float|string> $parameters
     */
    public function __construct(array $parameters = [])
    {
        foreach ($parameters as $key => $parameter) {
            $this->setParameter($key, $parameter);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $id, object $service): void
    {
        $this->values[$id] = $service;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id, int $behavior = self::EXCEPTION_ON_INVALID_REFERENCE): ?object
    {
        return $this->make($id, $behavior);
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
     * {@inheritDoc}
     */
    public function register(ServiceProviderInterface $provider): self
    {
        $provider->register($this);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameter(string $name): int|float|string|array
    {
        if (!$this->hasParameter($name)) {
            throw new \RuntimeException("The parameter '$name' not found.");
        }

        return $this->parameters[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function setParameter(string $name, float|int|string|array $value): void
    {
        if (\is_numeric($name)) {
            throw new \InvalidArgumentException("The parameter name cannot be numeric.");
        }

        $this->parameters[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setParameters(array $parameters): void
    {
        foreach ($parameters as $key => $parameter) {
            $this->setParameter($key, $parameter);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function hasParameter(string $name): bool
    {
        return \array_key_exists($name, $this->parameters);
    }

    private function make(string $id, int $behavior): ?object
    {
        if ($behavior === self::EXCEPTION_ON_INVALID_REFERENCE) {
            if (!isset($this->values[$id])) {
                throw new ServiceNotFoundException($id);
            }

            if (isset($this->factories[$id])) {
                $this->values[$id] = $this->factories[$id]($this);
            }

            return $this->values[$id];
        }

        return null;
    }
}
