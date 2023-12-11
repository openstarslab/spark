<?php

namespace Spark\Tests\Unit\Framework\Extension;

use PHPUnit\Framework\Attributes\CoversClass;
use Spark\Framework\Extension\ExtensionCollection;
use Spark\Framework\Extension\ExtensionList;
use Spark\Framework\Extension\Loader\ExtensionLoaderInterface;
use Spark\Tests\Unit\TestCase;

#[CoversClass(ExtensionList::class)]
class ExtensionListTest extends TestCase
{
    private ExtensionLoaderInterface $extensionLoaderMock;
    private ExtensionList $extensionList;

    protected function setUp(): void
    {
        $this->extensionLoaderMock = self::mock(ExtensionLoaderInterface::class);

        $this->extensionList = new ExtensionList(
            $this->extensionLoaderMock
        );
    }

    /**
     * Tests if all extensions are loaded correctly.
     */
    public function testLoadAll(): void
    {
        // Mock extension collection
        $extensions = new ExtensionCollection();
        // TODO: Add mock extensions to collection

        // Set the expectations for the mocks.
        $this->extensionLoaderMock->shouldReceive('activateExtensions')
            ->once()
            ->andReturn($extensions);

        // Load extensions and compare with mock extension collection
        $loadedExtensions = $this->extensionList->loadAll();

        self::assertEquals(
            $extensions->getActiveExtensions(), 
            $loadedExtensions, 
            'Loaded extensions do not match with mock extensions.'
        );
    }

    /**
     * Tests if no extensions are loaded when none are available.
     */
    public function testLoadAllNoExtensionsAvailable(): void
    {
        // Mock empty extension collection
        $extensions = new ExtensionCollection();

        // Set the expectations for the mocks.
        $this->extensionLoaderMock->shouldReceive('activateExtensions')
            ->once()
            ->andReturn($extensions);

        // Load extensions and compare with mock extension collection
        $loadedExtensions = $this->extensionList->loadAll();
        
        self::assertEmpty(
            $loadedExtensions, 
            'Extensions should not be loaded when none are available.'
        );
    }
}
