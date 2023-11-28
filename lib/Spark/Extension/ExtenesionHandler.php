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

namespace Spark\Extension;

use Composer\Autoload\ClassLoader;
use Spark\Extension\Exception\UnknownExtensionException;
use Spark\Extension\Loader\ExtenesionLoaderInterface;
use Spark\Extension\Loader\FilesystemExtenesionLoader;

abstract class ExtenesionHandler
{
    protected ExtenesionLoaderInterface $extensionLoader;

    public function __construct(
        protected string                    $root,
        protected ExtensionType             $type,
        protected ClassLoader $classLoader
    )
    {
        $this->extensionLoader = new FilesystemExtenesionLoader($this->root, $this->classLoader, $this->type);
        $this->extensionLoader->initializeExtenesions();
    }

    public function get(string $name): ExtensionInterface
    {
        if ($extenesion = $this->extensionLoader->getExtensionInstance($name)) {
            return $extenesion;
        }

        throw new UnknownExtensionException($name);
    }

    public function getList(): array
    {
        return $this->extensionLoader->getExtensionInstances();
    }
}