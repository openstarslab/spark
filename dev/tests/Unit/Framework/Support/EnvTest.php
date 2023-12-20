<?php

namespace Spark\Tests\Unit\Framework\Support;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use Spark\Framework\Support\Env;
use Spark\Tests\Unit\TestCase;

#[CoversClass(Env::class)]
#[Small]
class EnvTest extends TestCase
{
    public function testLoadThrowsExceptionWhenFileIsMissing(): void
    {
        self::expectException(\RuntimeException::class);

        $env = new Env();
        $env->load('test.env');
    }

    public function testGetEnvironmentVariable(): void
    {
        $env = new Env();
        $env->load(\dirname(__DIR__, 2) . '/Stubs');

        self::assertEquals('VALUE', Env::get('KEY'));
    }
}
