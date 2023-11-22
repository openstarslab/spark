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

namespace Spark\Core\Foundation;

use Nulldark\Container\Container;
use Spark\Contract\Foundation\Application as ApplicationContract;
use Spark\Core\Foundation\Providers\RoutingServiceProvider;
use Spark\Core\Foundation\Providers\ServiceProvider;

/**
 * Application
 *
 * The main class that orchestrates of the Framework functionality of the library.
 *
 * @since   2023-11-17
 * @version 0.1.0-alpha
 * @package Spark\Core\Foundation
 * @author  Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license https://opensource.org/license/lgpl-2-1/
 * @link    https://github.com/openstarslab/spark-core
 */
final class Application extends Container implements ApplicationContract
{
    public const VERSION = '0.1.0-alpha';

    /**
     * Indicates if the application has "booted".
     *
     * @var bool $booted
     */
    private bool $booted = false;

    /**
     * Indicates if the application has "bootstrapped".
     *
     * @var bool $booted
     */
    private bool $bootstrapped = false;

    /**
     * Array of service providers
     *
     * @var array<string, ServiceProvider> $serviceProviders
     */
    private array $serviceProviders = [];

    public function __construct()
    {
        parent::__construct();

        $this->registerBaseBindings();
        $this->registerBaseProviders();
    }

    /**
     * {@inheritDoc}
     */
    public function register(ServiceProvider $provider): ServiceProvider
    {
        if (($registered = $this->getProvider($provider)) !== null) {
            return $registered;
        }

        $provider->register();

        $this->setProviderAsRegistered($provider);

        if ($this->isBooted()) {
            $this->bootProvider($provider);
        }
        return $provider;
    }

    /**
     * {@inheritDoc}
     */
    public function getProvider(ServiceProvider $provider): ServiceProvider|null
    {
        if (array_key_exists(get_class($provider), $this->serviceProviders)) {
            return $this->serviceProviders[get_class($provider)];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function bootProvider(ServiceProvider $provider): void
    {
        if (method_exists($provider, 'boot')) {
            $provider->boot();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setProviderAsRegistered(ServiceProvider $provider): void
    {
        $this->serviceProviders[get_class($provider)] = $provider;
    }

    /**
     * {@inheritDoc}
     */
    public function boot(): self
    {
        if (!$this->isBooted()) {
            array_walk(
                $this->serviceProviders,
                fn (ServiceProvider $provider) => $this->bootProvider($provider)
            );

            $this->booted = true;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function bootstrapWith(array $bootstrappers): void
    {
        $this->bootstrapped = true;

        foreach ($bootstrappers as $bootstrapper) {
            $this->make($bootstrapper)->bootstrap($this);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * {@inheritDoc}
     */
    public function hasBeenBootstrapped(): bool
    {
        return $this->bootstrapped;
    }

    private function registerBaseBindings(): void
    {
        $this->singleton('app', $this);
    }

    private function registerBaseProviders(): void
    {
        $this->register(new RoutingServiceProvider($this));
    }
}
