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

namespace Spark\Framework\Extension;

final class ExtensionCollection
{
    /**
     * @var ExtensionInterface[] $extensions
     */
    private array $extensions = [];

    /**
     * Adds an extension to the extension's collection.
     *
     * @param ExtensionInterface $extension
     *  The extension to be added.
     * @return void
     */
    public function add(ExtensionInterface $extension): void
    {
        if ($this->has($extension::class)) {
            return;
        }

        $this->extensions[$extension::class] = $extension;
    }

    /**
     * Checks if an extension exists in the extension's collection.
     *
     * @param string $name
     *  The name of the extension to check.
     *
     * @return bool
     *  Returns `TRUE` if the extension exists, `FALSE` otherwise.
     */
    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->extensions);
    }

    /**
     * Retrieves all the extensions.
     *
     * @return ExtensionInterface[]
     *  An array containing all the extensions.
     */
    public function all(): array
    {
        return $this->extensions;
    }

    /**
     * Retrieves an extension by name.
     *
     * @param string $name
     *  The name of the extension.
     *
     * @return ExtensionInterface|null
     *  The extension with the given name, if found. NULL otherwise.
     */
    public function get(string $name): ?ExtensionInterface
    {
        return $this->extensions[$name] ?? null;
    }

    /**
     * Retrieves the active extensions.
     *
     * @return ExtensionInterface[]
     *   An array containing only the active extensions.
     */
    public function getActiveExtensions(): array
    {
        if (!$this->extensions) {
            return [];
        }

        return \array_filter($this->extensions, static fn (ExtensionInterface $extension) => $extension->isActive());
    }
}
