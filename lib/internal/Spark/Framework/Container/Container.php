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

use Spark\Framework\Container\Definition\Factory;
use Spark\Framework\Container\Definition\Parameter;
use Spark\Framework\Container\Definition\Reference;
use Spark\Framework\Container\Exception\ServiceNotFoundException;

class Container implements ContainerInterface
{
    protected array $values = [];
    protected array $instances = [];
    protected static ?ContainerInterface $instance = null;

    /**
     * Sets the shared instance of the container.
     *
     * @param ContainerInterface|null $instance
     *  The container instance.
     *
     * @return ContainerInterface|static
     */
    public static function setInstance(ContainerInterface $instance = null): ContainerInterface|static|null
    {
        return static::$instance = $instance;
    }

    /**
     * Gets current container instance.
     *
     * @return ContainerInterface|static|null
     */
    public static function getInstance(): ContainerInterface|static|null
    {
        if (!isset(self::$instance)) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * {@inheritDoc}
     */
    public function setParameter(string $name, float|int|string|array $value): void
    {
        if (\is_numeric($name)) {
            throw new \InvalidArgumentException("The parameter name cannot be numeric.");
        }

        $this->values[$name] = new Parameter($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $id, object $service): void
    {
        $this->instances[$id] = $service;
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
    public function make(string $id, int $behavior = self::EXCEPTION_ON_INVALID_REFERENCE): ?object
    {
        if ($behavior === self::EXCEPTION_ON_INVALID_REFERENCE) {
            if (\array_key_exists($id, $this->instances)) {
                return $this->instances[$id];
            }

            if (!isset($this->values[$id])) {
                throw new ServiceNotFoundException($id);
            }

            $concrete = $this->values[$id];

            if ($concrete instanceof Factory) {
                $value = ($concrete->callable)($this);

                if ($concrete->singleton === true) {
                    $this->instances[$id] = $concrete;
                }

                return $value;
            }

            return $concrete;
        }

        return $this->values[$id] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        return \array_key_exists($id, $this->values)
            || \array_key_exists($id, $this->instances);
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

        $parameter = $this->values[$name];

        if (!$parameter instanceof Parameter) {
            $reason = \sprintf(
                "The given parameter ('%s') is not instance of %s",
                $name,
                Parameter::class,
            );

            throw new \RuntimeException($reason);
        }

        return $parameter->value;
    }

    /**
     * {@inheritDoc}
     */
    public function hasParameter(string $name): bool
    {
        return \array_key_exists($name, $this->values) && $this->values[$name] instanceof Parameter;
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
    public function singleton(string $id, callable|object $concrete): void
    {
        $this->bind($id, $concrete, true);
    }

    /**
     * {@inheritDoc}
     */
    public function bind(string $id, callable|object $concrete, bool $singleton = null): void
    {
        if ($singleton === null) {
            $singleton = false;
        }


        $binding = match (true) {
            $concrete instanceof \Closure => new Factory($id, $concrete, $singleton),
            \is_object($concrete) => new Reference($concrete),
            default => throw new \InvalidArgumentException('Unknown concrete type.'),
        };

        $this->values[$id] = $binding;
    }
}
