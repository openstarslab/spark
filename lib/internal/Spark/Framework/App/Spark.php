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

namespace Spark\Framework\App;

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Spark\Framework\App\Application\Application;
use Spark\Framework\App\Exceptions\ErrorHandler;
use Spark\Framework\App\Filesystem\Directories;
use Spark\Framework\App\Filesystem\DirectoriesInterface;
use Spark\Framework\Container\Container;
use Spark\Framework\Http\Request;
use Spark\Framework\Support\Env;

/**
 * The Kernel class is responsible for initializing and starting the application.
 */
final class Spark extends Container
{
    private bool $booted = false;

    public function __construct(
        protected string $rootDir
    ) {
    }

    public static function create(string $rootDir): self
    {
        $self = new self(
            $rootDir,
        );

        $self->singleton(DirectoriesInterface::class, fn() => new Directories($rootDir));

        $self->registerDefaultProviders();

        return $self;
    }

    private function registerDefaultProviders(): void
    {
        foreach ($this->defaultProviders()->toArray() as $provider) {
            $this->register($provider);
        }
    }

    private function defaultProviders(): DefaultProviders
    {
        return new DefaultProviders();
    }

    /**
     * Starts the application.
     *
     * @param \Spark\Framework\App\Application\Application $application
     *  The application instance to start.
     *
     * @return void
     */
    public function start(Application $application): void
    {
        $this->envLoader()->load($this->rootDir);

        try {
            ErrorHandler::register();

            $request = Request::fromGlobals();
            $response = $application->start($request);

            $sapiEmitter = new SapiEmitter();
            $sapiEmitter->emit($response);
        } catch (\Throwable $e) {
            echo $e;
        }
    }

    private function envLoader(): Env
    {
        return new Env();
    }

    public function boot(): void
    {
        if ($this->booted === true) {
            return;
        }

        foreach ($this->serviceProviders as $provider) {
            $provider->boot();
        }

        $this->booted = true;
    }

    /**
     * Creates a new application of the specified type.
     *
     * @param class-string $type
     *  The type of application to create.
     *
     * @return \Spark\Framework\App\Application\Application
     *  The created application.
     */
    public function createApplication(string $type): Application
    {
        $application = $this->get($type);

        if (!($application instanceof Application)) {
            $reason = \sprintf(
                "The given type (%s) does not implement %s",
                $type,
                Application::class,
            );

            throw new \InvalidArgumentException($reason);
        }

        return $application;
    }
}
