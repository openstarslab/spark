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
    protected static ?ContainerInterface $instance = null;
    protected array $values = [];
    protected array $instances = [];
    protected array $serviceProviders = [];

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
                    $this->instances[$id] = $value;
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
    public function register(ServiceProvider|string $provider): ServiceProvider
    {
        if (($registered = $this->getProvider($provider)) !== null) {
            return $registered;
        }

        if (\is_string($provider)) {
            $provider = new $provider($this);

            if (!($provider instanceof ServiceProvider)) {
                $reason = \sprintf(
                    "The given provider does not extends %s",
                    ServiceProvider::class,
                );

                throw new \InvalidArgumentException($reason);
            }
        }

        $provider->register();

        if (\property_exists($provider, 'bindings')) {
            foreach ($provider->bindings as $key => $value) {
                $this->bind($key, $value);
            }
        }

        if (\property_exists($provider, 'singletons')) {
            foreach ($provider->singletons as $key => $value) {
                $this->singleton($key, $value);
            }
        }

        $this->serviceProviders[\get_class($provider)] = $provider;

        return $provider;
    }

    /**
     * Gets a service provider instance.
     *
     * @param string|\Spark\Framework\Container\ServiceProvider $provider
     * @return \Spark\Framework\Container\ServiceProvider|null
     */
    public function getProvider(string|ServiceProvider $provider): ?ServiceProvider
    {
        return $this->serviceProviders[\is_string($provider) ? $provider : \get_class($provider)] ?? null;
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

    /**
     * {@inheritDoc}
     */
    public function singleton(string $id, object $concrete): void
    {
        $this->bind($id, $concrete, true);
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
    public function setParameter(string $name, float|int|string|array $value): void
    {
        if (\is_numeric($name)) {
            throw new \InvalidArgumentException("The parameter name cannot be numeric.");
        }

        $this->values[$name] = new Parameter($name, $value);
    }
}
