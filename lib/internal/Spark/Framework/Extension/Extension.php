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

namespace Spark\Framework\Extension;

use Nulldark\Routing\RouteCollection;
use Spark\Framework\Container\ContainerAwareTrait;
use Spark\Framework\Container\ContainerInterface;

abstract class Extension implements ExtensionInterface
{
    use ContainerAwareTrait;

    protected string $name;
    protected string $path;
    protected bool $active = true;

    /**
     * {@inheritDoc}
     */
    public function boot(): void
    {
        $this->loadRoutes();
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of the extension.
     *
     * @param string $name
     *  The name to set for the extension.
     * @return self
     *  Returns an instance of the extension.
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the path for the extension.
     *
     * @param string $path
     *  The path to set.
     *
     * @return self
     *  This method returns the extension instance.
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * {@inheritDoc}
     */
    public function loadRoutes(): void
    {
        $routesPath = $this->getPath() . '/Resources/routes.php';

        if (\file_exists($routesPath)) {
            $routesLoader = require $routesPath;


            if (\is_callable($routesLoader)) {
                $routesLoader(
                    $this->container->get(RouteCollection::class),
                );

                return;
            }

            throw new \RuntimeException("To load routes, the routes.php file must return \Closure");
        }
    }
}
