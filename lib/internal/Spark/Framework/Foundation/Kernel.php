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
use Nulldark\Container\Container;
use Psr\Container\ContainerInterface;
use Spark\Framework\DependencyInjection\Builder\ContainerBuilder;
use Spark\Framework\DependencyInjection\Builder\ContainerBuilderInterface;
use Spark\Framework\Extension\ExtensionInterface;
use Spark\Framework\Foundation\Application\ApplicationInterface;

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

    /** @var array $extensions */
    private array $extensions = [];

    /**
     * The container instance.
     *
     * @param null|\Psr\Container\ContainerInterface $container
     */
    private ?ContainerInterface $container = null;

    public function __construct(
        private readonly string $environment,
        private readonly string $rootDir,
        private readonly ClassLoader $classLoader
    ) {

    }

    public static function create(string $environment, string $rootDir, ClassLoader $classLoader): self
    {
        $bootstrap = new self(
          $environment,
          $rootDir,
          $classLoader
        );

        $bootstrap->boot();

        return $bootstrap;
    }

    /**
     * @inheritDoc
     */
    public function createApplication(string $type): ApplicationInterface
    {
        return $this->container->get($type);
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->initializeExtensions();
        $this->initializeContainer();

        foreach ($this->extensions as $extension) {
            $extension->setContainer($this->container);
            $extension->boot();
        }

        $this->booted = true;
    }

    /**
     * @inheritDoc
     */
    public function start(ApplicationInterface $application): void
    {
        $application->start();
    }

    /**
     * Gets a new instance of container builder.
     *
     * @return ContainerBuilderInterface
     *  The container builder instance.
     */
    private function getContainerBuilder(): ContainerBuilderInterface
    {
        $container = new ContainerBuilder();
        $container->register(ContainerInterface::class, Container::class);

        return $container;
    }

    /**
     * Loads system extensions from file and gets a new instance.
     *
     * @return ExtensionInterface[]
     */
    private function registerExtensions(): iterable
    {
        $extensions = require $this->rootDir . '/app/config/extensions.php';

        foreach ($extensions as $extension) {
            yield new $extension();
        }
    }

    /**
     * Initializes the container by setting up the necessary services and parameters.
     *
     * @return void
     */
    private function initializeContainer(): void
    {
        $container = $this->getContainerBuilder();
        $container->set(ClassLoader::class, $this->classLoader);
        $container->set('kernel.modules_dir', $this->rootDir . '/app/modules/');

        foreach ($this->extensions as $extension) {
            $extension->register($container);
        }

        $this->container = $container->build();
    }

    /**
     * Initializes the core extensions for the application.
     *
     * @return void
     */
    private function initializeExtensions(): void
    {
        foreach ($this->registerExtensions() as $extension) {
            $this->extensions[] = $extension;
        }
    }

}