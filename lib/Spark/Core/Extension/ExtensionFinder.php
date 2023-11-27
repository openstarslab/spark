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

namespace Spark\Core\Extension;

use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Composer\Package\CompletePackageInterface;
use FilesystemIterator;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Spark\Core\Extension\Composer\PackageProvider;
use Spark\Core\Extension\Exception\InvalidComposerException;

use function in_array;
use function str_ends_with;
use function str_starts_with;

final readonly class ExtensionFinder implements ExtensionFinderInterface
{
    private PackageProvider $packageProvider;

    public function __construct(
        private string $root
    )
    {
        $this->packageProvider = new PackageProvider();
    }

    /**
     * @inheritDoc
     */
    public function loadExtenesions(string $extensionPath, IOInterface $io = null): iterable
    {
        if ($io === null) {
            $io = new NullIO();
        }

        $extensions = [];

        foreach ($this->scanDirectory($extensionPath) as $extension) {
            $extensionPath = $extension->getRealPath();

            try {
                $package = $this->packageProvider->getComposerPackage($extensionPath, $io);
            } catch (InvalidComposerException) {
                continue;
            }

            if (!$this->isSparkExtenesionType($package) || !$this->isExtenesionComposerValid($package)) {
                continue;
            }

            $extensionName = $this->getExtenesionNameFromPackage($package);

            $extensions[$extensionName] = [
                'baseClass' => $extensionName,
                'path' => $extension->getPath(),
                'composerPackage' => $package
            ];
        }

        return $extensions;
    }

    /**
     * @inheritDoc
     */
    public function scanDirectory(string $directory): iterable
    {
        $absoluteDirectory = ($directory === '' ? $this->root : $this->root . "/$directory");

        $flags = FilesystemIterator::UNIX_PATHS;
        $flags |= FilesystemIterator::SKIP_DOTS;
        $flags |= FilesystemIterator::FOLLOW_SYMLINKS;
        $flags |= FilesystemIterator::CURRENT_AS_SELF;

        $directoryIterator = new RecursiveDirectoryIterator($absoluteDirectory, $flags);

        $filter = new RecursiveCallbackFilterIterator(
            $directoryIterator,
            function (RecursiveDirectoryIterator $directory) {
                $name = $directory->getFilename();

                if (str_starts_with(".", $name)) {
                    return false;
                }

                if ($directory->isDir()) {
                    return true;
                }

                return str_ends_with($name, 'composer.json');
            }
        );

        return new RecursiveIteratorIterator(
            $filter,
            RecursiveIteratorIterator::LEAVES_ONLY,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );
    }

    private function isSparkExtenesionType(CompletePackageInterface $package): bool
    {
        return in_array($package->getType(), [
            ExtensionType::EXTENESION_COMPOSER_TYPE,
            ExtensionType::PLUGIN_COMPOSER_TYPE
        ]);
    }

    private function isExtenesionComposerValid(CompletePackageInterface $package): bool
    {
        $extra = $package->getExtra();

        return isset($extra[ExtensionType::SPARK_EXTENSION_CLASS_EXTRA_IDENTIFIER])
            && $extra[ExtensionType::SPARK_EXTENSION_CLASS_EXTRA_IDENTIFIER] !== '';
    }

    private function getExtenesionNameFromPackage(CompletePackageInterface $package): string
    {
        return $package->getExtra()[ExtensionType::SPARK_EXTENSION_CLASS_EXTRA_IDENTIFIER];
    }
}