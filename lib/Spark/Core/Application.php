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

namespace Spark\Core;

use Composer\Factory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use LogicException;
use Nulldark\Container\Container;
use Nulldark\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spark\Core\Providers\CoreServiceProvider;
use Spark\Core\Providers\EventServiceProvider;
use Spark\Core\Providers\ExtensionServiceProvider;
use Spark\Core\Providers\LogServiceProvider;
use Spark\Core\Providers\RoutingServiceProvider;

use Spark\Http\MiddlewareDispatcher;
use function dirname;
use function realpath;
use function sprintf;


/**
 * Class Application
 *
 * This class represents the application. It implements the ApplicationInterface.
 * The application is responsible for booting and running the application.
 */
class Application implements ApplicationInterface, HttpKernelInterface
{
    /**
     * Indicates if the application has "booted".
     *
     * @var bool $booted
     */
    protected bool $booted = false;

    /**
     * Application root dir.
     *
     * @var string $projectDir
     */
    protected string $projectDir;

    /**
     * Service providers.
     *
     * @var ServiceProvider[] $providers
     */
    protected array $providers = [];

    /**
     * Container.
     *
     * @var ContainerInterface|null $container
     */
    protected ?ContainerInterface $container = null;

    /**
     * Middleware dispatcher.
     *
     * @var MiddlewareDispatcher $middlewareDispatcher
     */
    protected MiddlewareDispatcher $middlewareDispatcher;

    public function __construct(MiddlewareDispatcher $middlewareDispatcher = null)
    {
        $this->middlewareDispatcher  = $middlewareDispatcher ?: new MiddlewareDispatcher(...$this->middlewares());
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        if ($this->container === null) {
            $this->preBoot();
        }

        foreach ($this->providers as $provider) {
            $provider->setContainer($this->container);
            $provider->boot();
        }

        $this->booted = true;
    }

    /**
     * @inheritDoc
     */
    public function middlewares(): iterable
    {
        // @TODO: move this to separately file.
        return [

        ];
    }

    private function preBoot(): void
    {
        $this->initializeProviders();
        $this->initializeContainer();

        if ($this->container !== null) {
            \Spark::setContainer($this->container);
        }
    }

    protected function initializeProviders(): void
    {
        $this->providers = [];

        foreach ($this->registerProviders() as $provider) {
            $name = $provider::class;

            if (isset($this->providers[$name])) {
                throw new LogicException(sprintf('Trying to register two providers with the same name "%s".', $name));
            }

            $this->providers[$name] = $provider;
        }
    }

    protected function registerProviders(): iterable
    {
        // @TODO: move this to separately file.

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
        $container = $this->buildContainer();
        $container->singleton('kernel', $this);

        $this->container = $container;
    }

    protected function buildContainer(): ContainerInterface
    {
        $container = new Container();
        $container->singleton(ContainerInterface::class, $container);
        $container->singleton(ApplicationInterface::class, $this);
        $container->singleton(self::class, $this);

        $container->singleton('class_loader', include $this->getProjectDir() . '/vendor/autoload.php');

        foreach ($this->getKernelParameters() as $key => $value) {
            $container->scalar($key, $value);
        }

        foreach ($this->providers as $provider) {
            $provider->register($container);
        }

        return $container;
    }

    protected function getKernelParameters(): array
    {
        return [
            'kernel.project_dir' => $this->getProjectDir(),
            'kernel.app_dir' => $this->getAppDir(),
            'kernel.logs_dir' => $this->getLogsDir(),
            'kernel.cache_dir' => $this->getCacheDir()
        ];
    }

    public function getAppDir(): string
    {
        return $this->getProjectDir() . '/app';
    }

    /**
     * Returns the project directory.
     *
     * @return string
     *  The absolute path of the project directory.
     */
    public function getProjectDir(): string
    {
        if (!isset($this->projectDir)) {
            $this->projectDir = dirname((string)realpath(Factory::getComposerFile()));
        }

        return $this->projectDir;
    }

    /**
     * Sets the project directory.
     *
     * @param string $projectDir
     *  The path to the project directory.
     *
     * @return $this
     */
    public function setProjectDir(string $projectDir): self
    {
        $this->projectDir = rtrim($projectDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * Returns the path to the logs directory.
     *
     * @return string
     *  The absolute path to the logs directory.
     */
    public function getLogsDir(): string
    {
        return $this->getProjectDir() . '/var/log';
    }


    /**
     * Returns the absolute path to the cache directory.
     *
     * @return string
     *  The absolute path to the cache directory.
     */
    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache';
    }

    /**
     * @inheritDoc
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->booted === false) {
            $this->boot();
        }

        $response = $this->middlewareDispatcher->handle($request);

        $emitter = new SapiEmitter();
        $emitter->emit($response);

        return $response;
    }
}
