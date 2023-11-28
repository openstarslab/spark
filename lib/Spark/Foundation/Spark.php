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

use Composer\Autoload\ClassLoader;
use Nulldark\Container\Container;
use Nulldark\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spark\Events\EventServiceProvider;
use Spark\Extension\ExtensionServiceProvider;
use Spark\Foundation\Providers\CoreServiceProvider;
use Spark\Foundation\Providers\ServiceProvider;
use Spark\Log\LogServiceProvider;
use Spark\Routing\RoutingServiceProvider;

class Spark implements SparkInterface
{
    /**
     * Indicates if the application has "booted".
     *
     * @var bool $booted
     */
    protected bool $booted = false;

    /**
     * The base path of project.
     *
     * @var string $basePath
     */
    public string $basePath;

    /**
     * Array of service providers
     *
     * @var array<string, ServiceProvider> $serviceProviders
     */
    private array $serviceProviders = [];

    /**
     * Service container.
     *
     * @var \Nulldark\Container\ContainerInterface $container
     */
    protected ContainerInterface $container;

    public function __construct(string $rootDir, ClassLoader $classLoader)
    {
        $this->basePath = rtrim($rootDir, '\/');

        $this->container = new Container();
        $this->container->singleton('class_loader', $classLoader);
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getAppPath(): string
    {
        return $this->getBasePath() . '/app';
    }

    public function getLogsPath(): string
    {
        return $this->getBasePath() . '/var/log';
    }

    public function getCachePath(): string
    {
        return $this->getBasePath() . '/var/cache';
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

        if ($this->booted) {
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
     * @inheritDoc
     */
    public function boot(): void
    {
        if ($this->booted === true) {
            return;
        }

        $this->initializeContainer();
        $this->initializeServiceProviders();

        \array_walk(
            $this->serviceProviders, function ($provider) {
                $this->bootProvider($provider);
            }
        );

        $this->booted = true;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->booted === false) {
            $this->boot();
        }

        return $this->container->get('http_kernel')->handle($request);
    }

    protected function registerServiceProviders(): iterable
    {
        foreach ([
            CoreServiceProvider::class,
            LogServiceProvider::class,
            EventServiceProvider::class,
            RoutingServiceProvider::class,
            ExtensionServiceProvider::class
        ] as $provider) {
            yield new $provider();
        }
    }

    protected function initializeContainer(): void
    {
        $this->container = $this->container ?: new Container();
        $this->container->singleton(ContainerInterface::class, $this->container);

        foreach ([
            'kernel.base_path' => $this->getBasePath(),
            'kernel.app_path' => $this->getAppPath(),
            'kernel.cache_path' => $this->getCachePath(),
            'kernel.logs_path' => $this->getLogsPath()
        ] as $key => $value) {
            $this->container->scalar($key, $value);
        }
    }

    protected function initializeServiceProviders(): void
    {
        foreach ($this->registerServiceProviders() as $provider) {
            $provider->setContainer($this->container);
            $this->register($provider);
        }
    }
}
