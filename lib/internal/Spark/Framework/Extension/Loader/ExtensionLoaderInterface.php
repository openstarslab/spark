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

use Spark\Framework\Extension\ExtensionCollection;
use Spark\Framework\Extension\ExtensionInterface;

interface ExtensionLoaderInterface
{
    /**
     * Activates all extensions.
     *
     * @return \Spark\Framework\Extension\ExtensionCollection
     */
    public function activateExtensions(): ExtensionCollection;

    /**
     * Retrieves all instances of loaded extensions.
     *
     * @return \Spark\Framework\Extension\ExtensionCollection
     *  Returns a collection of instances of loaded extensions.
     */
    public function getExtensionInstances(): ExtensionCollection;

    /**
     * Returns an instance of the extension with the given name.
     *
     * @param string $name
     *     The name of the extension.
     *
     * @return null|\Spark\Framework\Extension\ExtensionInterface
     *     Returns an instance of the extension with the given name.
     */
    public function getExtensionInstance(string $name): ?ExtensionInterface;
}
