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

namespace Spark\Framework\Extension\Loader;

use Composer\Autoload\ClassLoader;
use Spark\Framework\Extension\Exception\ExtensionLoaderException;
use Spark\Framework\Extension\ExtensionCollection;
use Spark\Framework\Extension\ExtensionInterface;
use Spark\Framework\Extension\Finder\ExtensionFinder;
use Spark\Framework\Extension\Finder\ExtensionFinderInterface;

final class ExtensionLoader implements ExtensionLoaderInterface
{
    protected bool $initialized = false;

    protected ExtensionCollection $extensions;

    public function __construct(
        protected readonly string $extensionDir,
        protected readonly ClassLoader $classLoader,
    ) {
        $this->extensions = new ExtensionCollection();
    }

    /**
     * @inheritDoc
     */
    public function getExtensionInstances(): ExtensionCollection
    {
        return $this->extensions;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionInstance(string $name): ?ExtensionInterface
    {
        $extension = $this->extensions->get($name);

        if ($extension === null || $extension->isActive()) {
            return null;
        }

        return $extension;
    }

    /**
     * @inheritDoc
     */
    public function activateExtensions(): void
    {
        if ($this->initialized === true) {
            return;
        }

        $extensionsData = $this->getExtensionFinder()->loadExtensionsData($this->extensionDir);

        foreach ($extensionsData as $extensionData) {
            $extensionClassName = $extensionData['baseClass'];
            $extensionClassPath = $extensionData['path'];

            if (!\class_exists($extensionClassName) || !\file_exists($extensionClassPath)) {
                continue;
            }

            $extension = new $extensionClassName(
                (string) $extensionData['name'],
                (string) $extensionData['path'],
                true,
            );

            if (!$extension instanceof ExtensionInterface) {
                $reason = sprintf(
                    "Extension class %s must implements %s",
                    $extensionClassName,
                    ExtensionInterface::class,
                );

                throw new ExtensionLoaderException($extensionData['name'], $reason);
            }

            $this->extensions->add($extension);
        }

        $this->initialized = true;
    }

    /**
     * Returns an instance of ExtensionFinderInterface that is used to find extensions.
     *
     * @return ExtensionFinderInterface
     *  An object implementing the ExtensionFinderInterface.
     */
    public function getExtensionFinder(): ExtensionFinderInterface
    {
        return new ExtensionFinder();
    }
}
