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

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Implementation of event dispatcher interface.
 */
class EventDispatcher implements EventDispatcherInterface
{
    protected array $sorted = [];
    protected array $listeners = [];


    public function listen(string $eventName, callable $listener, int $priority = 0): void
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);
    }

    /**
     * @inheritDoc
     */
    public function dispatch(object $event, string $eventName = null): object
    {
        $eventName ??= $event::class;

        if ($listeners = $this->getListenersForEvent($eventName)) {
            $this->invokeListeners($listeners, $eventName, $event);
        }

        return $event;
    }

    /**
     * @inheritDoc
     */
    public function getListenersForEvent(string $eventName): array
    {
        if (empty($this->listeners[$eventName])) {
            return [];
        }

        if (!isset($this->sorted[$eventName])) {
            $this->sortListners($eventName);
        }

        return $this->sorted[$eventName];
    }

    /**
     * Sorts the listeners for given event by the priority.
     *
     * @param string $eventName
     *  An event name.
     *
     * @return void
     */
    protected function sortListners(string $eventName): void
    {
        \krsort($this->listeners[$eventName]);
        $this->sorted[$eventName] = [];

        foreach ($this->listeners[$eventName] as &$listeners) {
            foreach ($listeners as &$listener) {
                $this->sorted[$eventName][] = $listener;
            }
        }
    }

    /**
     * Triggers the listeners for given an event.
     *
     * @param callable[] $listeners
     *  The event listeners.
     * @param string     $eventName
     *  The event name.
     * @param object     $event
     *  The event object to pass to the listener.
     *
     * @return void
     */
    protected function invokeListeners(iterable $listeners, string $eventName, object $event): void
    {
        $stoppable = $event instanceof StoppableEventInterface;

        foreach ($listeners as $listener) {
            if ($stoppable && $event->isPropagationStopped()) {
                break;
            }

            $listener($event, $eventName, $this);
        }
    }
}
