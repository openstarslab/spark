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

namespace Spark\Cache\Backend;

use Spark\Cache\CacheItem;

/**
 * Defines memory cache backend implementation.
 *
 * For testing purpose.
 */
class MemoryBackend extends AbstractBackend
{
    /** @var CacheItem[] $values  */
    private array $values = [];

    /**
     * @inheritDoc
     */
    protected function doFetch(array $keys): iterable
    {
        $values = [];

        foreach ($keys as $key) {
            if (\array_key_exists($key, $this->values) && $this->values[$key] !== null) {
                $values[$key] = $this->values[$key];
            }
        }

        return $values;
    }

    /**
     * @inheritDoc
     */
    protected function doDelete(array $keys): bool
    {
        foreach ($keys as $key) {
            unset($this->values[$key]);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function doClear(): bool
    {
        $this->values = [];

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function doHave(string $key): bool
    {
        return \array_key_exists($key, $this->values);
    }

    /**
     * @inheritDoc
     */
    protected function doSave(CacheItem $item): bool
    {
        $this->values[$item->getKey()] = $item;

        return true;
    }
}