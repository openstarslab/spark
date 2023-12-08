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

namespace Spark\Framework\Extension\Finder;

use Composer\IO\IOInterface;
use Composer\InstalledVersions;
use Composer\Package\CompletePackageInterface;
use Spark\Framework\Composer\PackageProvider;
use Spark\Framework\Extension\Exception\InvalidComposerException;

class ExtensionFinder implements ExtensionFinderInterface
{
    public const COMPOSER_TYPE = 'spark-extension';
    public const SPARK_EXTENSION_CLASS_EXTRA_IDENTIFIER = 'spark-extension-class';

    private PackageProvider $packageProvider;

    public function __construct()
    {
        $this->packageProvider = new PackageProvider();
    }

    /**
     * @inheritDoc
     */
    public function loadExtensionsData(string $extensionPath, IOInterface $io = null): iterable
    {
        $extensions = InstalledVersions::getInstalledPackagesByType(self::COMPOSER_TYPE);
        $extensionsInfos = [];

        foreach ($extensions as $extension) {
            $path = InstalledVersions::getInstallPath($extension);
            $extensionPath = $path . '/composer.json';

            try {
                $package = $this->packageProvider->getComposerPackage($extensionPath, $io);
            } catch (InvalidComposerException) {
                continue;
            }

            if ($this->isSparkExtension($package) && $this->isExtensionComposerValid($package)) {
                $extensionName = $this->getExtensionNameFromPackage($package);
                $extensionsInfos[$extensionName] = [
                    'name' => $extensionName,
                    'path' => $path,
                    'baseClass' => $extensionName,
                    'composerPackage' => $package,
                ];
            }
        }

        return $extensionsInfos;
    }

    /**
     * Determines whether a given package is a Spark extension.
     *
     * @param CompletePackageInterface $package
     *  The package to check.
     *
     * @return bool
     *  True if the package is a Spark extension, false otherwise.
     */
    private function isSparkExtension(CompletePackageInterface $package): bool
    {
        return $package->getType() === self::COMPOSER_TYPE;
    }

    /**
     * Check if the extension composer is valid.
     *
     * @param CompletePackageInterface $package
     *  The composer package to check.
     *
     * @return bool
     *  Returns true if the extension composer is valid, false otherwise.
     */
    private function isExtensionComposerValid(CompletePackageInterface $package): bool
    {
        $extra = $package->getExtra();

        return isset($extra[self::SPARK_EXTENSION_CLASS_EXTRA_IDENTIFIER])
            && $extra[self::SPARK_EXTENSION_CLASS_EXTRA_IDENTIFIER] !== '';
    }

    /**
     * Get the extension name from a composer package.
     *
     * @param CompletePackageInterface $package
     *  The composer package.
     *
     * @return string
     *  The extension name.
     */
    private function getExtensionNameFromPackage(CompletePackageInterface $package): string
    {
        return $package->getExtra()[self::SPARK_EXTENSION_CLASS_EXTRA_IDENTIFIER];
    }
}
