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

namespace Spark\Framework\Cache\Backend;

use Spark\Framework\Cache\CacheItem;

/**
 * Defines APCu cache backend implementation.
 *
 * @phpstan-type CacheEntry array{data: mixed}
 */
class ApcuBackend extends AbstractBackend
{
    /**
     * {@inheritDoc}
     */
    protected function doFetch(array $keys): iterable
    {
        $values = [];
        $results = \apcu_fetch($keys, $ok);

        if (!\is_array($results)) {
            $results = [$results];
        }

        foreach ($results as $key => $value) {
            if ($ok && $value !== null) {
                /** @var CacheEntry $entry */
                $entry = \unserialize($value);
                $values[$key] = $entry['data'];
            }
        }

        return $values;
    }

    /**
     * @inheritDoc
     */
    protected function doHave(string $key): bool
    {
        return \apcu_exists($key);
    }

    /**
     * @inheritDoc
     */
    protected function doDelete(array $keys): bool
    {
        foreach ($keys as $key) {
            \apcu_delete($key);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function doClear(): bool
    {
        return \apcu_clear_cache();
    }

    /**
     * @inheritDoc
     */
    protected function doSave(CacheItem $item): bool
    {
        $entry = ['data' => $item->get()];

        return \apcu_store(
            $item->getKey(),
            \serialize($entry),
            (int)$item->getExpirationTime(),
        );
    }
}
