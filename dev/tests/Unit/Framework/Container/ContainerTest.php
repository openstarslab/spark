<?php

namespace Spark\Tests\Unit\Framework\Container;

use PHPUnit\Framework\Attributes\CoversClass;
use Spark\Framework\App\Spark;
use Spark\Framework\Container\Container;
use Spark\Framework\Container\Exception\ServiceNotFoundException;
use Spark\Framework\Container\ServiceProvider;

#[CoversClass(Container::class)]
class ContainerTest extends \Spark\Tests\Unit\TestCase
{
    private Container $container;

    public function testRegisterMethod(): void
    {
        $provider = self::mock(ServiceProvider::class, new Spark(__DIR__));
        $provider->expects('register');

        $result = $this->container->register($provider);

        self::assertSame($provider, $result);
    }

    /**
     * Test case for container factory method.
     * The callable should be added into factories array.
     */
    public function testFactoryMethod(): void
    {
        $callable = static fn() => new \stdClass();

        $this->container->bind(\stdClass::class, $callable);

        // By getting 'test' service from container,
        // we indirectly test that the factory has correctly been stored
        $service = $this->container->get(\stdClass::class);

        self::assertInstanceOf(\stdClass::class, $service);
    }

    /**
     * Test case for container factory method.
     * If factory is not found, a `ServiceNotFoundException` should be thrown.
     */
    public function testFactoryMethodWithNonExistentFactory(): void
    {
        self::expectException(ServiceNotFoundException::class);

        // Try getting a non-existent service
        $this->container->get(\stdClass::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();
    }
}
