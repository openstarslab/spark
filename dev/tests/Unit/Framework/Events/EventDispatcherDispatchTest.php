<?php

namespace Spark\Tests\Unit\Framework\Events;

use PHPUnit\Framework\Attributes\CoversClass;
use Psr\EventDispatcher\StoppableEventInterface;
use Spark\Framework\Events\Event;
use Spark\Framework\Events\EventDispatcher;

#[CoversClass(EventDispatcher::class)]
class EventDispatcherDispatchTest extends \Spark\Tests\Unit\TestCase
{
    private EventDispatcher $eventDispatcher;

    protected function setUp(): void
    {
        $this->eventDispatcher = new EventDispatcher();
    }

    public function testDispatchShouldInvokeListenerOnEventDispatch(): void
    {
        $invoked = false;

        $this->eventDispatcher->listen('test.event', function () use (&$invoked) {
            $invoked = true;
        });

        $event = new class {
        };
        $this->eventDispatcher->dispatch($event, 'test.event');

        self::assertTrue($invoked, 'Listener should be invoked on event dispatch');
    }

    public function testDispatchShouldNotInvokeListenerAfterPropagationStopped(): void
    {
        $invokedAfterStop = false;

        $this->eventDispatcher->listen('test.event', function ($event) {
            if ($event instanceof Event) {
                $event->stopPropagation();
            }
        });

        $this->eventDispatcher->listen('test.event', function () use (&$invokedAfterStop) {
            $invokedAfterStop = true;
        });

        $event = new class extends Event {
            private bool $propagationStopped = false;

            public function isPropagationStopped(): bool
            {
                return $this->propagationStopped;
            }

            public function stopPropagation(): void
            {
                $this->propagationStopped = true;
            }
        };

        $this->eventDispatcher->dispatch($event, 'test.event');

        self::assertFalse($invokedAfterStop, 'Listener should not be invoked after propagation is stopped');
    }
}
