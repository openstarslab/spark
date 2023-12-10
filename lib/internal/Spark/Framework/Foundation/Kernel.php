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

namespace Spark\Framework\Foundation;

use Composer\Autoload\ClassLoader;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Spark\Framework\Container\Container;
use Spark\Framework\Container\ContainerInterface;
use Spark\Framework\Extension\ExtensionInterface;
use Spark\Framework\Extension\ExtensionList;
use Spark\Framework\Extension\Loader\ExtensionLoaderInterface;
use Spark\Framework\Foundation\Application\ApplicationInterface;
use Spark\Framework\Foundation\Exceptions\ErrorHandler;
use Spark\Framework\Foundation\Providers\ExtensionServiceProvider;
use Spark\Framework\Http\Request;

/**
 * The Kernel class is responsible for initializing and starting the application.
 */
final class Kernel implements KernelInterface
{
    /**
     * Indicates if the application has "booted".
     *
     * @var bool $booted
     */
    protected bool $booted = false;

    /**
     * The container instance.
     *
     * @param \Spark\Framework\Container\ContainerInterface $container
     */
    private ContainerInterface $container;

    public function __construct(
        private readonly string $environment,
        private readonly string $rootDir,
    ) {
        $this->container = new Container(
            $this->getKernelParameters(),
        );
    }

    public static function create(string $environment, string $rootDir, ClassLoader $classLoader): self
    {
        $bootstrap = new self(
            $environment,
            $rootDir,
        );

        $bootstrap->boot();

        return $bootstrap;
    }

    /**
     * @inheritDoc
     */
    public function createApplication(string $type): ApplicationInterface
    {
        $application = $this->container->get($type);

        if (!($application instanceof ApplicationInterface)) {
            $reason = \sprintf(
                "The given type (%s) does not implement %s",
                $type,
                ApplicationInterface::class,
            );

            throw new \InvalidArgumentException($reason);
        }

        return $application;
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        foreach ($this->registerServiceProviders() as $provider) {
            $this->container->register($provider);
        }

        foreach ($this->container->get(ExtensionList::class)->loadALl() as $extension) {
            $extension->register($this->container);
        }

        $this->booted = true;
    }

    /**
     * @inheritDoc
     */
    public function start(ApplicationInterface $application): void
    {
        ErrorHandler::register();

        $this->bootsExtensions();

        $request = Request::fromGlobals();
        $response = $application->start($request);

        // emit response.
        $sapiEmitter = new SapiEmitter();
        $sapiEmitter->emit($response);
    }

    private function bootsExtensions(): void
    {
        foreach ($this->container->get(ExtensionList::class)->loadAll() as $extension) {
            $extension->setContainer($this->container);
            $extension->boot();
        }
    }

    /**
     * Loads system extensions from file and gets a new instance.
     *
     * @return \Spark\Framework\Container\ServiceProviderInterface[]
     */
    private function registerServiceProviders(): iterable
    {
        /** @var class-string<\Spark\Framework\Container\ServiceProviderInterface>[] $providers */
        $providers = require $this->rootDir . '/app/config/providers.php';

        foreach ($providers as $provider) {
            yield new $provider();
        }
    }

    /**
     * @return string[]
     */
    private function getKernelParameters(): array
    {
        return [
            'kernel.environment' => $this->environment,
            'kernel.root_dir' => $this->rootDir,
            'kernel.extension_dir' => $this->rootDir . '/app/src/',
        ];
    }
}
