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

namespace Spark\Events;

/**
 * Defines event dispatcher interface
 *
 * @see https://www.php-fig.org/psr/psr-14/
 */
interface EventDispatcherInterface extends \Psr\EventDispatcher\EventDispatcherInterface
{
    /**
     * Registers an event listener with the event dispatcher.
     *
     * @param string $eventName
     *  An event name.
     * @param callable $listener
     *  A listener for given event.
     * @param int $priority
     *  The higher the priority, the earlier the listener will be triggered.
     *
     * @return void
     */
    public function listen(string $eventName, callable $listener, int $priority = 0): void;

    /**
     * Returns the listeners for specific event.
     *
     * @param string $eventName
     *  An event name.
     *
     * @return callable[]
     *   Array of listeners.
     */
    public function getListenersForEvent(string $eventName): array;

    /**
     * Provide all relevant listeners with an event to process.
     *
     * @template T of object
     *
     * @param T $event
     *  The event to pass to the event listeners
     * @param string $eventName
     *  An event name.
     *
     * @return T
     *  The passed $event MUST be returned
     */
    public function dispatch(object $event, string $eventName = null): object;
}