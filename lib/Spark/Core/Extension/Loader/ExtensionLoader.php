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

namespace Spark\Core\Extension\Loader;

use Composer\Autoload\ClassLoader;
use Spark\Core\Extension\Exception\ExtenesionLoaderException;
use Spark\Core\Extension\ExtensionInterface;
use Spark\Core\Extension\ExtensionType;
use function assert;
use function class_exists;
use function count;
use function is_string;
use function mb_strpos;
use function sprintf;

abstract class ExtensionLoader implements ExtenesionLoaderInterface
{
    protected bool $initialized = false;
    protected array $extensionInfos = [];
    protected array $extensionInstances = [];

    public function __construct(
        protected readonly string        $root,
        protected readonly ClassLoader   $classLoader,
        protected readonly ExtensionType $type
    )
    {

    }

    /**
     * @inheritDoc
     */
    public function getExtensionInstances(): array
    {
        return $this->extensionInstances;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionInstance(string $name): ?ExtensionInterface
    {
        $extenesion = $this->extensionInstances[$name];

        if (!$extenesion || !$extenesion->isActive()) {
            return null;
        }

        return $extenesion;
    }

    /**
     * @inheritDoc
     */
    final public function initializeExtenesions(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->loadExtensionsInfos();
        $this->registerNamespaces();
        $this->instantiateExtenesions();

        $this->initialized = true;
    }

    abstract public function loadExtensionsInfos(): void;

    private function registerNamespaces(): void
    {
        foreach ($this->extensionInfos as $extension) {
            assert(is_string($extension['baseClass']));
            $extensionName = $extension['name'] ?? $extension['baseClass'];

            if (!isset($extension['autoload'])) {
                $reason = sprintf(
                    'Unable to register extension "%s" in autoload. Required property `autoload` missing.',
                    $extension['baseClass']
                );

                throw new ExtenesionLoaderException($extensionName, $reason);
            }

            $psr4 = $extension['autoload']['psr-4'] ?? [];

            if (count($psr4) === 0) {
                $reason = sprintf(
                    'Unable to register extension "%s" in autoload. Required property `psr-4` missing.',
                    $extension['baseClass']
                );

                throw new ExtenesionLoaderException($extensionName, $reason);
            }

            foreach ($psr4 as $namespace => $paths) {
                if (is_string($paths)) {
                    $paths = [$paths];
                }

                $mappedPaths = $this->autoloadPathMaps($extensionName, $paths, $extension['path']);
                $this->classLoader->addPsr4($namespace, $mappedPaths);
                if ($this->classLoader->isClassMapAuthoritative()) {
                    $this->classLoader->setClassMapAuthoritative(false);
                }
            }
        }
    }

    private function autoloadPathMaps(string $extension, array $paths, string $extensionPath): array
    {
        $mappedPaths = [];

        if (mb_strpos($extensionPath, $this->root) !== 0) {
            throw new ExtenesionLoaderException(
                $extension,
                sprintf('Extension dir %s needs to be a sub-directory of the dir %s', $extensionPath, $this->root)
            );
        }

        foreach ($paths as $path) {
            $mappedPaths[] = $extensionPath . DIRECTORY_SEPARATOR . $path;
        }

        return $mappedPaths;
    }

    private function instantiateExtenesions(): void
    {
        foreach ($this->extensionInfos as $extenesionData) {
            $className = $extenesionData['baseClass'];

            if (!class_exists($className)) {
                continue;
            }

            $extenesion = new $className((bool)$extenesionData['active'], $extenesionData['path']);

            if (!$extenesion instanceof ExtensionInterface) {
                $reason = sprintf("Extenesion class %s must implements %s",
                    $extenesion::class,
                    ExtensionInterface::class
                );

                throw new ExtenesionLoaderException($extenesionData['name'], $reason);
            }

            $this->extensionInstances[$className] = $extenesion;
        }
    }
}