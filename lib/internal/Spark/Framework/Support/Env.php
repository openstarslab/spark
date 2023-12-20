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

namespace Spark\Framework\Support;

class Env
{
    /**
     * Gets the value of an environment variable.
     *
     * @param string $key
     * @param string|null $default
     *
     * @return string|null
     */
    public static function get(string $key, string $default = null): string|null {
        return match(true) {
            \array_key_exists($key, $_SERVER) => $_SERVER[$key],
            \array_key_exists($key, $_ENV) => $_ENV[$key],
            default => \getenv($key) ?? $default
        };
    }

    /**
     * Parses the content of a .env file.
     *
     * @param string $dir
     *  The path to the .env file.
     * @param string $file
     *  Custom name of .env file, defaults is .env
     *
     * @return void
     */
    public function load(string $dir, string $file = '.env'): void
    {
        $filename = \rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;

        if (!\file_exists($filename)) {
            throw new \RuntimeException("Missing .env file");
        }

        $entries = $this->parse($filename);

        foreach ($entries as $name => $value) {
            switch (true) {
                case \getenv($name, true) !== false: \putenv("$name=$value");
                case empty($_SERVER[$name]): $_SERVER[$name] = $value;
                case empty($_ENV[$name]): $_ENV[$name] = $value;
            };
        }
    }

    /**
     * Parses a file content.
     *
     * @param string $filename
     *
     * @return string[]
     */
    private function parse(string $filename): array
    {
        $entries = [];

        $lines = \file($filename, \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (\str_starts_with($line, '#')) {
                continue; // is comments
            }

            if (\str_contains($line, '=')) {
                [$key, $value] = \explode('=', $line, 2);

                $key = \preg_replace('/[^a-zA-Z0-9_]/', '', \trim($key));
                $value = \trim($value);

                $entries[$key] = $value;
            }
        }

        return $entries;
    }
}