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

namespace Spark\Framework\App\Filesystem;

final class Directories implements DirectoriesInterface
{
    /** @var string $root */
    private string $root;

    /** @var string[] $directories */
    private array $directories;

    public function __construct(string $root, array $directories = [])
    {
        $this->root = $root;

        $directories = \array_merge([
            self::APP => '/app/',
            self::CONFIG => '/app/config/',
            self::PUBLIC => '/public/',
            self::VAR => '/var/',
            self::CACHE => '/var/cache/',
            self::LOGS => '/var/logs/',
        ], $directories);

        foreach ($directories as $name => $directory) {
            $this->set($name, $directory);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, string $path): void
    {
        $this->directories[$name] = $this->joinPaths(
            $this->normalizePath($path),
        );
    }

    /**
     * Joins a given path with the root path.
     *
     * @param string $path
     *  The path to be joined with the root path.
     *
     * @return string
     *  The combined path.
     */
    private function joinPaths(string $path): string
    {
        return $this->root . ($path != '' ? '/' . \ltrim($path, '/') : '');
    }

    /**
     * Normalizes a given file path by replacing
     * backslashes and consecutive forward slashes with a single forward slash.
     *
     * @param string $path
     *  The file path to normalize.
     *
     * @return string
     *  The normalized file path.
     */
    private function normalizePath(string $path): string
    {
        return \str_replace(['\\', '//'], '/', $path);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name): string
    {
        if (!$this->has($name)) {
            throw new \Exception("Unknown directory '%name'");
        }

        return $this->directories[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->directories);
    }
}
