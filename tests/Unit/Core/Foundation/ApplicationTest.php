<?php

namespace Spark\Tests\Unit\Core\Foundation;

use PHPUnit\Framework\Attributes\CoversClass;
use Spark\Contracts\Foundation\ApplicationContract;
use Spark\Core\Foundation\Application;
use Spark\Tests\Unit\TestCase;

#[CoversClass(Application::class)]
class ApplicationTest extends TestCase
{
    public function testShouldImplementApplicationContract(): void
    {
        $app = new Application();

        self::assertInstanceOf(ApplicationContract::class, $app);
    }
}
