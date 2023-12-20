<?php

namespace Spark\Tests\Unit\Framework\Extension\Finder;

use Composer\IO\IOInterface;
use Composer\Package\CompletePackageInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Spark\Framework\Composer\PackageProvider;
use Spark\Framework\Extension\Exception\InvalidComposerException;
use Spark\Framework\Extension\Finder\ExtensionFinder;
use Spark\Tests\Unit\TestCase;

#[CoversClass(ExtensionFinder::class)]
class ExtensionFinderTest extends TestCase
{
    private ExtensionFinder $extensionFinder;
    private PackageProvider&MockInterface $packageProviderMock;

    public function testLoadExtensionsDataReturnsIterableWhenPathIsValid(): void
    {
        $io = self::mock(IOInterface::class);
        $dummyPackage = self::mock(CompletePackageInterface::class);

        $dummyPackage->shouldReceive('getType')
            ->andReturn(ExtensionFinder::COMPOSER_TYPE);

        $dummyPackage->shouldReceive('getExtra')
            ->andReturn([
                ExtensionFinder::SPARK_EXTENSION_CLASS_EXTRA_IDENTIFIER =>
                    'DummyExtension',
            ]);

        $this->packageProviderMock->shouldReceive('getComposerPackage')
            ->andReturn($dummyPackage);

        $result = $this->extensionFinder->loadExtensionsData(
            \dirname(__DIR__, 3) . '/Stubs/extensions',
            $io,
        );

        self::assertIsArray($result);
        self::assertNotEmpty($result);
    }

    public function testLoadExtensionsDataSkipsInvalidComposers(): void
    {
        $io = self::mock(IOInterface::class);

        $this->packageProviderMock->shouldReceive('getComposerPackage')
            ->andThrow(InvalidComposerException::class);

        $result = $this->extensionFinder->loadExtensionsData(
            \dirname(__DIR__, 3) . '/Stubs/extensions',
            $io,
        );

        self::assertEmpty($result);
    }

    protected function setUp(): void
    {
        $this->packageProviderMock = self::mock(PackageProvider::class);
        $this->extensionFinder = new ExtensionFinder($this->packageProviderMock);
    }
}
