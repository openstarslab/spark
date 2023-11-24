<?php

namespace Spark\Core\Cache\Backend;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Spark\Core\Cache\CacheBackendInterface;
use Spark\Core\Cache\CacheItem;


/**
 * It is an implementation of the `CacheItemPoolInterface` interface, but also
 * defines abstract methods for implementation in a particular CacheBackend.
 */
abstract class AbstractBackend implements CacheBackendInterface, CacheItemPoolInterface
{
    protected static \Closure $createCacheItem;

    /** @var array<string, CacheItem> $deferred */
    protected array $deferred = [];

    public function __construct() {
        self::$createCacheItem ??= \Closure::bind(static function (string $key, mixed $value, bool $isHit) {
            $item = new CacheItem();
            $item->key = $key;
            $item->value = $value;
            $item->isHit = $isHit;

            return $item;
        }, null, CacheItem::class);
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys
     *  An indexed array of keys of items to retrieve.
     *
     * @return iterable
     *  A traversable collection of Cache Items keyed by the cache keys of each item.
     */
    abstract protected function doFetch(array $keys): iterable;

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
     * Deletes all items from pool.
     *
     * @return bool
     *  `TRUE` if the pool was successfully cleared, otherwise `FALSE`.
     */
    abstract protected function doClear(): bool;

    /**
     * Saves item into pool.
     *
     * @param CacheItem $item
     *  Cache item to the save.
     *
     * @return array|bool
     *  The identifiers that failed to be cached or a boolean stating if caching succeeded or not.
     */
    abstract protected function doSave(CacheItem $item): array|bool;

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function set(string $key, mixed $value,  null|int|\DateInterval $tls= null): bool
    {
        $item = $this->getItem($key);
        $item->set($value);
        $item->expiresAfter($tls);

        return $this->save($item);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        return $this->deleteItem($key);
    }


    /**
     * @inheritDoc
     */
    public function getItem(string $key): CacheItem
    {
        if (isset($this->deferred[$key])) {
            $this->commit();
        }

        $isHit = false;
        $value = null;

        try {
            foreach ($this->doFetch([$key]) as $value) {
                $isHit = true;
            }

            return (self::$createCacheItem)($key, $value, $isHit);
        } catch (\Exception $e) {

        }

        return (self::$createCacheItem)($key, null, false);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function deleteItem(string $key): bool
    {
        return $this->deleteItems([$key]);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function hasItem(string $key): bool
    {
        if (isset($this->deferred[$key])) {
            $this->commit();
        }

        return $this->doHave($key);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function clear(): bool
    {
        $this->deferred = [];

        return $this->doClear();
    }

    /**
     * @inheritDoc
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
}