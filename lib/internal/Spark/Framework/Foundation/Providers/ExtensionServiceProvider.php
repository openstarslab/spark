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

namespace Spark\Framework\Foundation\Providers;

use Spark\Framework\Container\ContainerInterface;
use Spark\Framework\Container\ServiceProviderInterface;
use Spark\Framework\Extension\ExtensionList;
use Spark\Framework\Extension\Loader\ExtensionLoader;
use Spark\Framework\Extension\Loader\ExtensionLoaderInterface;

class ExtensionServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(ContainerInterface $container): void
    {
        $extensionDir = $container->getParameter('kernel.extension_dir');

        if (!\is_string($extensionDir) || !\file_exists($extensionDir)) {
            throw new \RuntimeException("The directory to extensions has invalid value, must be valid path.");
        }

        $container->factory(ExtensionLoaderInterface::class, fn(ContainerInterface $container) =>
            new ExtensionLoader(
                $extensionDir,
            )
        );

        $container->factory(ExtensionList::class, fn(ContainerInterface $container) => new ExtensionList(
            $container->get(ExtensionLoaderInterface::class),
        ));
    }
}
