<?php

namespace Spark\Tests\Unit\Framework\Extension;

use PHPUnit\Framework\Attributes\CoversClass;
use Spark\Framework\Extension\ExtensionCollection;
use Spark\Framework\Extension\ExtensionInterface;
use Spark\Tests\Unit\TestCase;

#[CoversClass(ExtensionCollection::class)]
final class ExtensionCollectionTest extends TestCase
{
    public function testAddAndHas(): void
    {
        $collection = self::mock(ExtensionCollection::class);

        $mockExtension = self::mock(ExtensionInterface::class);

        $mockExtension
            ->shouldReceive('class')
            ->andReturn('MockExtension');

        $collection
            ->shouldReceive('add')
            ->with($mockExtension);

        $collection
            ->shouldReceive('has')
            ->with('MockExtension')
            ->andReturn(true);


        self::assertTrue(
            $collection->has('MockExtension'),
        );
    }

    public function testAll(): void
    {
        $collection = self::mock(ExtensionCollection::class);
        $collection
            ->shouldReceive('all')
            ->andReturn([]);

        self::assertIsArray($collection->all());
    }

    public function testGet(): void
    {
        $mockExtension = self::mock(ExtensionInterface::class);

        $mockExtension
            ->shouldReceive('class')
            ->andReturn('MockExtension');

        $collection = self::mock(ExtensionCollection::class);

        $collection
            ->shouldReceive('add')
            ->with($mockExtension);

        $collection->add($mockExtension);

        $collection
            ->shouldReceive('get')
            ->with('MockExtension')
            ->andReturn($mockExtension);

        $retrievedExtension = $collection->get('MockExtension');

        self::assertInstanceOf(
            ExtensionInterface::class,
            $retrievedExtension,
        );
    }

    public function testGetActiveExtensions(): void
    {
        $activeMockExtension = self::mock(ExtensionInterface::class);

        $activeMockExtension
            ->shouldReceive('class')
            ->andReturn('ActiveMockExtension');

        $activeMockExtension
            ->shouldReceive('isActive')
            ->andReturn(true);

        $collection = self::mock(ExtensionCollection::class);
        $collection
            ->shouldReceive('add')
            ->with($activeMockExtension);

        $collection->add($activeMockExtension);

        $collection
            ->shouldReceive('getActiveExtensions')
            ->andReturn([$activeMockExtension]);

        $activeExtensions = $collection->getActiveExtensions();

        self::assertContains(
            $activeMockExtension,
            $activeExtensions,
        );
    }
}
