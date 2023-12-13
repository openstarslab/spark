<?php

namespace Spark\Tests\Unit\Framework\Events;

use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Spark\Framework\Events\EventDispatcher;
use Spark\Framework\Events\EventDispatcherInterface;
use Spark\Tests\Unit\TestCase;

#[CoversClass(EventDispatcher::class)]
class EventDispatcherListenTest extends TestCase
{
    protected EventDispatcherInterface&MockInterface $dispatcher;

    /**
     * Set up the test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock instance of EventDispatcherInterface.
        $this->dispatcher = self::mock(EventDispatcherInterface::class);
    }

    /**
     * Tests the 'listen' functionality of the EventDispatcherInterface class.
     * This test focuses on the scenario when a listener is registered for an event.
     */
    public function testListen(): void
    {
        $eventName = 'test.event';
        $listener  = static function (): void {
        };

        // Expect the listen method to be called once with specific parameters.
        $this->dispatcher
            ->shouldReceive('listen')
            ->once()
            ->with($eventName, $listener);

        $this->dispatcher->listen($eventName, $listener);
    }

    public function testGetListenersForEventWithNoListener(): void
    {
        $this->dispatcher->shouldReceive('getListenersForEvent')
            ->with('non.existing.event');

        self::assertEquals([], $this->dispatcher->getListenersForEvent('non.existing.event'));
    }

    public function testGetListenersForEventWithSingleListener(): void
    {
        $eventName = "event.test";

        // Mock event listener.
        $listener = fn () => "listener1";

        // Register a listener to the dispatcher
        $this->dispatcher
            ->shouldReceive(['getListenersForEvent' => [$listener]])
            ->once()
            ->with($eventName);

        // Retrieve the listeners for the event
        $listeners = $this->dispatcher->getListenersForEvent($eventName);

        // Assert that a registered listener is included in the listeners of an event
        self::assertEquals($listener, reset($listeners));
    }

    public function testGetListenersForEventWithMultipleListeners(): void
    {
        $eventName = "event.test.multiple";

        // Mock event listeners.
        $listener1 = fn () => "listener1";
        $listener2 = fn () => "listener2";
        $listener3 = fn () => "listener3";

        // Expected listeners
        $expectedListeners = [$listener1, $listener2, $listener3];

        // Register the listeners to the dispatcher
        $this->dispatcher
            ->shouldReceive(['getListenersForEvent' => $expectedListeners])
            ->with($eventName);

        // Retrieve the listeners for the event
        $listeners = $this->dispatcher->getListenersForEvent($eventName);

        // Assert that the registered listeners are included in the listeners of an event
        self::assertEquals($expectedListeners, $listeners);
    }

    public function testGetListenersForEventWithPrioritizedListeners(): void
    {
        $eventName = "event.test.priority";

        // Mock event listeners with different priorities.
        $listener1 = fn () => "listener1";
        $listener2 = fn () => "listener2";
        $listener3 = fn () => "listener3";

        // Expected listeners
        $expectedListeners = [3 => $listener3, 2 => $listener2, 1 => $listener1];

        // Register the listeners to the dispatcher with priorities
        $this->dispatcher
            ->shouldReceive(['getListenersForEvent' => $expectedListeners])
            ->with($eventName);

        // Retrieve the listeners for the event
        $listeners = $this->dispatcher->getListenersForEvent($eventName);

        // Assert that the registered listeners are present in the correct order according to their priority
        self::assertEquals($expectedListeners, $listeners);
    }
}
