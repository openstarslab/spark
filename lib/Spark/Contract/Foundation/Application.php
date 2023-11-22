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

namespace Spark\Contract\Foundation;

use Nulldark\Container\ContainerInterface;
use Spark\Core\Foundation\Providers\ServiceProvider;

/**
 * Application
 *
 * @since   2023-11-17
 * @package Spark\Contract\Foundation
 * @author  Dominik Szamburski <dominikszamburski99@gmail.com>
 * @license https://opensource.org/license/lgpl-2-1/
 * @link    https://github.com/openstarslab/spark-core
 */
interface Application extends ContainerInterface
{
    /**
     * Registers a service provider with the application.
     *
     * @param \Spark\Core\Foundation\Providers\ServiceProvider $provider
     *  Service provider instance.
     *
     * @return \Spark\Core\Foundation\Providers\ServiceProvider
     *  Returns registered service provider.
     */
    public function register(ServiceProvider $provider): ServiceProvider;

    /**
     * Sets given service provider as registered.
     *
     * @param \Spark\Core\Foundation\Providers\ServiceProvider $provider
     *
     * @return void
     */
    public function setProviderAsRegistered(ServiceProvider $provider): void;

    /**
     * Gets the registered service provider instance if not exists returns `NULL`.
     *
     * @param \Spark\Core\Foundation\Providers\ServiceProvider $provider
     *
     * @return \Spark\Core\Foundation\Providers\ServiceProvider|null
     *  Returns a service provider, if not found return `NULL`.
     */
    public function getProvider(ServiceProvider $provider): ServiceProvider|null;

    /**
     * Boots a service provider.
     *
     * @param \Spark\Core\Foundation\Providers\ServiceProvider $provider
     *  Service provider instance.
     *
     * @return void
     */
    public function bootProvider(ServiceProvider $provider): void;

    /**
     * Boots an application service providers.
     *
     * @return $this
     */
    public function boot(): self;

    /**
     * Runs given bootstrap classes.
     *
     * @param string[] $bootstrappers
     *  Array of bootstrap classes.
     *
     * @return void
     */
    public function bootstrapWith(array $bootstrappers): void;

    /**
     * Determine if the application is booted.
     *
     * @return bool
     *  Returns `TRUE` if application is booted, otherwise `FALSE`.
     */
    public function isBooted(): bool;

    /**
     * Determine if the application has been bootstrapped.
     *
     * @return bool
     *  Returns `TRUE` if application has been bootstrapped, otherwise `FALSE`.
     */
    public function hasBeenBootstrapped(): bool;
}
