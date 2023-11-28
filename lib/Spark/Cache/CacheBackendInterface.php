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

namespace Spark\Cache;

/**
 * Defines an interface for cache implementations.
 */
interface CacheBackendInterface
{
    /**
     * Fetches a value from the cache.
     *
     * @template T
     *
     * @param string $key
     *  The key of item to gets from cache.
     * @param (callable(\Psr\Cache\CacheItemInterface,bool):T) $callback
     *  On cache misses, a callback is called that should return missing value.
     *
     * @return T
     */
    public function get(string $key, callable $callback): mixed;

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string $key
     *  The key of the item to store.
     * @param mixed $value
     *  The value of the item to store, must be serializable.
     * @param int|\DateInterval|null $tls
     *  The TTL value of this item.
     *
     * @return bool
     *  `TRUE` on success, otherwise `FALSE`.
     */
    public function set(string $key, mixed $value,  null|int|\DateInterval $tls = null): bool;

    /**
     * Removes an item from cache.
     *
     * @param string $key
     *  The key of item to removes from cache.
     *
     * @return bool
     *  Returns `TRUE` if the was successfully removed, otherwise `FALSE`.
     */
    public function delete(string $key): bool;
}