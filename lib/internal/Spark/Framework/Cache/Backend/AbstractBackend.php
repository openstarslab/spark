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

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Spark\Framework\Cache\CacheBackendInterface;
use Spark\Framework\Cache\CacheItem;

/**
 * It is an implementation of the `CacheItemPoolInterface` interface, but also
 * defines abstract methods for implementation in a particular CacheBackend.
 */
abstract class AbstractBackend implements CacheBackendInterface, CacheItemPoolInterface
{
    protected static \Closure $createCacheItem;

    /**
     * A variable representing deferred tasks.
     *
     * @var CacheItem[] $deferred
     */
    protected array $deferred = [];

    public function __construct()
    {
        self::$createCacheItem ??= \Closure::bind(
            static function (string $key, mixed $value, bool $isHit) {
                $item = new CacheItem();
                $item->key = $key;
                $item->value = $value;
                $item->isHit = $isHit;

                return $item;
            },
            null,
            CacheItem::class,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key, callable $callback): mixed
    {
        $item = $this->getItem($key);

        if (!$item->isHit()) {
            $item->set($callback($item, true));
            $this->save($item);
        }

        return $item->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getItem(string $key): CacheItem
    {
        if (isset($this->deferred[$key])) {
            $this->commit();
        }

        $isHit = false;
        $hit = null;

        try {
            foreach ($this->doFetch([$key]) as $value) {
                $isHit = true;
                $hit = $value;
            }

            return (self::$createCacheItem)($key, $hit, $isHit);
        } catch (\Exception $e) {
        }

        return (self::$createCacheItem)($key, null, false);
    }

    /**
     * {@inheritDoc}
     */
    public function commit(): bool
    {
        $saved = true;

        foreach ($this->deferred as $item) {
            if (!$this->save($item)) {
                $saved = false;
            }
        }

        $this->deferred = [];

        return $saved;
    }

    /**
     * {@inheritDoc}
     */
    public function save(CacheItemInterface $item): bool
    {
        if (!$item instanceof CacheItem) {
            return false;
        }

        $results = $this->doSave($item);

        return \is_array($results) ? false : $results;
    }

    /**
     * Saves item into pool.
     *
     * @param \Spark\Framework\Cache\CacheItem $item
     *  Cache item to the save.
     *
     * @return string[]|bool
     *  The identifiers that failed to be cached or a boolean stating if caching succeeded or not.
     */
    abstract protected function doSave(CacheItem $item): array|bool;

    /**
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys
     *  An indexed array of keys of items to retrieve.
     *
     * @return CacheItem[]
     *  A traversable collection of Cache Items keyed by the cache keys of each item.
     */
    abstract protected function doFetch(array $keys): iterable;

    /**
     * {@inheritDoc}
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $tls = null): bool
    {
        $item = $this->getItem($key);
        $item->set($value);
        $item->expiresAfter($tls);

        return $this->save($item);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key): bool
    {
        return $this->deleteItem($key);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteItem(string $key): bool
    {
        return $this->deleteItems([$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteItems(array $keys): bool
    {
        $deleted = true;
        foreach ($keys as $key) {
            unset($this->deferred[$key]);

            if ($this->doDelete([$key])) {
                continue;
            }

            $deleted = false;
        }

        return $deleted;
    }

    /**
     * Removes the items from the pool.
     *
     * @param string[] $keys
     *  The keys to delete.
     *
     * @return bool
     *  TRUE` if the item was successfully deleted, otherwise `FALSE`.
     */
    abstract protected function doDelete(array $keys): bool;

    /**
     * {@inheritDoc}
     */
    public function getItems(array $keys = []): iterable
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }

        return $items;
    }

    /**
     * {@inheritDoc}
     */
    public function hasItem(string $key): bool
    {
        if (isset($this->deferred[$key])) {
            $this->commit();
        }

        return $this->doHave($key);
    }

    /**
     * Checks if the cache item exists in the pool.
     *
     * @param string $key
     *  The identifier for which to check existence
     *
     * @return bool
     *  `TRUE` if item found, otherwise `FALSE`.
     */
    abstract protected function doHave(string $key): bool;

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        if (!$item instanceof CacheItem) {
            return false;
        }

        $this->deferred[$item->getKey()] = $item;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): bool
    {
        $this->deferred = [];

        return $this->doClear();
    }

    /**
     * Deletes all items from pool.
     *
     * @return bool
     *  `TRUE` if the pool was successfully cleared, otherwise `FALSE`.
     */
    abstract protected function doClear(): bool;
}
