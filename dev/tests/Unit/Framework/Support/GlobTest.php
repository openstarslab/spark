<?php

namespace Spark\Tests\Unit\Framework\Support;

use PHPUnit\Framework\Attributes\CoversClass;
use Spark\Framework\Support\Glob;
use Spark\Tests\Unit\TestCase;

#[CoversClass(Glob::class)]
class GlobTest extends TestCase
{
    /**
     * Test glob method returns a empty array
     */
    public function testNonMatchingGlobReturnsEmptyArray(): void
    {
        $results = Glob::glob(
            '/root/{,*.}.php',
            Glob::GLOB_BRACE,
        );

        self::assertEquals([], $results);
    }

    /**
     * Test glob method with additional flags.
     */
    public function testGlobWithFlags(): void
    {
        $pattern = '*.php';
        $expected = \glob($pattern, \GLOB_NOSORT);

        $result = Glob::glob($pattern, Glob::GLOB_NOSORT);

        self::assertEquals($expected, $result);
    }

    /**
     * Test glob method with invalid pattern.
     */
    public function testGlobWithInvalidPattern(): void
    {
        self::expectException(\RuntimeException::class);

        Glob::glob(
            '/' . str_repeat('spark', 10000),
        );
    }
}
