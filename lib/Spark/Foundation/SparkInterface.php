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

namespace Spark\Foundation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spark\Foundation\Providers\ServiceProvider;

interface SparkInterface
{
    /**
     * Boots the current application.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Handles incoming request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *  HTTP Request.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface;

    /**
     * Registers a service provider with the application.
     *
     * @param \Spark\Foundation\Providers\ServiceProvider $provider
     *  Service provider instance.
     *
     * @return \Spark\Foundation\Providers\ServiceProvider
     *  Returns registered service provider.
     */
    public function register(ServiceProvider $provider): ServiceProvider;

    /**
     * Sets given service provider as registered.
     *
     * @param \Spark\Foundation\Providers\ServiceProvider $provider
     *  An service provider.
     *
     * @return void
     */
    public function setProviderAsRegistered(ServiceProvider $provider): void;

    /**
     * Gets the registered service provider instance if not exists returns `NULL`.
     *
     * @param \Spark\Foundation\Providers\ServiceProvider $provider
     *
     * @return \Spark\Foundation\Providers\ServiceProvider|null
     *  Returns a service provider, if not found return `NULL`.
     */
    public function getProvider(ServiceProvider $provider): ServiceProvider|null;

    /**
     * Boots a service provider.
     *
     * @param \Spark\Foundation\Providers\ServiceProvider $provider
     *  Service provider instance.
     *
     * @return void
     */
    public function bootProvider(ServiceProvider $provider): void;
}
