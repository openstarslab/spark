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
use Composer\Package\CompletePackageInterface;
use Spark\Framework\Composer\PackageProvider;
use Spark\Framework\Extension\Exception\InvalidComposerException;

class ExtensionFinder implements ExtensionFinderInterface
{
    public const COMPOSER_TYPE = 'spark-extension';
    public const SPARK_EXTENSION_CLASS_EXTRA_IDENTIFIER = 'spark-extension-class';

    public function __construct(
        private readonly PackageProvider $packageProvider = new PackageProvider()
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function loadExtensionsData(string $extensionPath, IOInterface $io = null): iterable
    {
        $extensions = $this->scanDirectory($extensionPath);
        $extensionsInfos = [];

        foreach ($extensions as $extension) {
            $path = $extension->getPath();
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
     * Scans the specified directory and returns an iterable of all files that match the filter criteria.
     *
     * @param string $directory
     *   The directory to be scanned.
     *
     * @return \SplFileInfo[]
     *   An iterable of files and directories that match the filter criteria.
     */
    public function scanDirectory(string $directory): iterable
    {
        $flags = \FilesystemIterator::UNIX_PATHS;
        $flags |= \FilesystemIterator::SKIP_DOTS;
        $flags |= \FilesystemIterator::FOLLOW_SYMLINKS;
        $flags |= \FilesystemIterator::CURRENT_AS_SELF;

        $directoryIterator = new \RecursiveDirectoryIterator($directory, $flags);

        $filter = new \RecursiveCallbackFilterIterator(
            $directoryIterator,
            function (\RecursiveDirectoryIterator $directory) {
                $name = $directory->getFilename();

                if (str_starts_with(".", $name)) {
                    return false;
                }

                if ($directory->isDir()) {
                    return true;
                }

                return str_ends_with($name, 'composer.json');
            },
        );

        return new \RecursiveIteratorIterator(
            $filter,
            \RecursiveIteratorIterator::LEAVES_ONLY,
            \RecursiveIteratorIterator::CATCH_GET_CHILD,
        );
    }

    /**
     * Determines whether a given package is a Spark extension.
     *
     * @param \Composer\Package\CompletePackageInterface $package
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
     * @param \Composer\Package\CompletePackageInterface $package
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
     * @param \Composer\Package\CompletePackageInterface $package
     *  The composer package.
     *
     * @return string
     *  The extension name.
     *
     * @throws \Spark\Framework\Extension\Exception\InvalidComposerException
     */
    private function getExtensionNameFromPackage(CompletePackageInterface $package): string
    {
        $extensionClass = $package->getExtra()[self::SPARK_EXTENSION_CLASS_EXTRA_IDENTIFIER];

        if ($extensionClass === "" || !\is_string($extensionClass)) {
            $reason = \sprintf(
                "composer.json has invalid extra/%s value.",
                self::SPARK_EXTENSION_CLASS_EXTRA_IDENTIFIER,
            );
            throw new InvalidComposerException($reason);
        }

        return $extensionClass;
    }
}
