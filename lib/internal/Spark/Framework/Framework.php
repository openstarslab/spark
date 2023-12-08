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

namespace Spark\Framework;

use Composer\Autoload\ClassLoader;
use Spark\Framework\DependencyInjection\Builder\ContainerBuilderInterface;
use Spark\Framework\Extension\Extension;
use Spark\Framework\Extension\Loader\ExtensionLoader;

class Framework extends Extension
{
    private array $modules = [];

    public function register(ContainerBuilderInterface $container): void
    {
        $extensionLoader = new ExtensionLoader(
            $container->get('kernel.modules_dir'),
            $container->get(ClassLoader::class),
        );

        $extensionLoader->activateExtensions();

        foreach ($extensionLoader->getExtensionInstances()->getActiveExtensions() as $extension) {
            $this->modules[$extension->getName()] = $extension;
            $this->modules[$extension->getName()] ->register($container);
        }

        parent::register($container);
    }

    public function boot(): void
    {
        foreach ($this->modules as $module) {
            $module->setContainer($this->container);
            $module->boot();
        }
    }
}
