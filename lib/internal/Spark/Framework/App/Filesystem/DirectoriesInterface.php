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

interface DirectoriesInterface
{
    public const PUBLIC = 'public';
    public const VAR = 'var';
    public const LOGS = 'logs';
    public const CACHE = 'cache';

    /**
     * Updates the value of the specified directory by name.
     *
     * @param string $name
     *     The name of the directory to update.
     * @param string $path
     *     The new path to be assigned to the directory.
     *
     * @return void
     */
    public function set(string $name, string $path): void;

    /**
     * Checks if the specified directory exists.
     *
     * @param string $name
     *   The name of the directory to check.
     *
     * @return bool
     *   True if the directory exists, false otherwise.
     */
    public function has(string $name): bool;

    /**
     * Retrieves the value of the specified directory by name.
     *
     * @param string $name
     *  The name of the directory to retrieve.
     *
     * @return string
     *  The value of the directory.
     */
    public function get(string $name): string;
}
