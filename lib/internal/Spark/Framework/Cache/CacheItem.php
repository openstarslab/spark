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

namespace Spark\Framework\Cache;

use Psr\Cache\CacheItemInterface;

/**
 * Defines object in the cache.
 *
 * @see https://www.php-fig.org/psr/psr-6/
 */
final class CacheItem implements CacheItemInterface
{
    protected string $key;
    protected mixed $value = null;
    protected bool $isHit = false;
    protected int|float|null $expiry = null;

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function get(): mixed
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isHit(): bool
    {
        return $this->isHit;
    }

    /**
     * @inheritDoc
     */
    public function set(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        $this->expiry = null !== $expiration ? (float)$expiration->format('U.u') : null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter(\DateInterval|int|null $time): static
    {
        if ($time instanceof \DateInterval) {
            if (($datetime = \DateTimeImmutable::createFromFormat('U', '0')) !== false) {
                $this->expiry = \microtime(true) + (float)$datetime->add($time)->format('U.u');
            }
        } else {
            $this->expiry = match (true) {
                \is_null($time) => null,
                \is_int($time) => $time + \microtime(true),
                default => throw new \InvalidArgumentException(
                    \sprintf(
                        'Expiration date must be an integer, a DateInterval or null, "%s" given.',
                        \get_debug_type($time)
                    )
                )
            };
        }

        return $this;
    }

    /**
     * Returns expiration time.
     *
     * @return float|int|null
     */
    public function getExpirationTime(): float|int|null
    {
        return $this->expiry;
    }
}
