<?php

namespace Spark\Tests\Unit\Framework\App\Filesystem;

use PHPUnit\Framework\Attributes\CoversClass;
use Spark\Framework\App\Filesystem\Directories;
use Spark\Framework\App\Filesystem\DirectoriesInterface;
use Spark\Tests\Unit\TestCase;

#[CoversClass(Directories::class)]
class DirectoriesTest extends TestCase
{
    private DirectoriesInterface $directories;

    /**
     * Test for set method of Directories class.
     * It starts by creating a directory and verifying it doesn't exist.
     * Then it uses the set method to add it and asserts that it now exists.
     */
    public function testSetMethodAddsDirectory(): void
    {
        $this->directories->set('test', '/path');

        self::assertFalse($this->directories->has('test2'));
        self::assertTrue($this->directories->has('test'));
        self::assertEquals('/root/path', $this->directories->get('test'));
    }

    /**
     * Test for set method of Directories class when directory path already exists.
     * It verifies that set method updates the path of the directory.
     */
    public function testSetMethodUpdatesDirectoryPath(): void
    {
        $this->directories->set('public', '/public/');
        self::assertEquals('/root/public/', $this->directories->get('public'));

        $this->directories->set('public', '/pub/');
        self::assertEquals('/root/pub/', $this->directories->get('public'));
    }

    /**
     * Tests that `has` function returns true if directory exists
     */
    public function testHasReturnsTrueWhenDirectoryExists(): void
    {
        self::assertTrue($this->directories->has('temp'));
    }

    /**
     * Tests that `has` function returns false if directory does not exists
     */
    public function testHasReturnsFalseWhenDirectoryDoesNotExists(): void
    {
        self::assertFalse($this->directories->has('nonexistent_directory'));
    }

    public function testGetMethod(): void
    {
        self::assertEquals('/root/tmp/', $this->directories->get('temp'));

        self::expectException(\Exception::class);
        $this->directories->get('unexisting_directory');
    }

    /**
     * setUp method called before running each test case.
     */
    protected function setUp(): void
    {
        $this->directories = new Directories('/root', [
            'temp' => '/tmp/',
        ]);
    }
}
