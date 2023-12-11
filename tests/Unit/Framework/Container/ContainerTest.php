<?php

namespace Spark\Tests\Unit\Framework\Container;

use PHPUnit\Framework\Attributes\CoversClass;
use Spark\Framework\Container\Container;
use Spark\Framework\Container\Exception\ServiceNotFoundException;
use Spark\Framework\Container\ServiceProviderInterface;

#[CoversClass(Container::class)]
class ContainerTest extends \Spark\Tests\Unit\TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();
    }

    public function testRegisterMethod(): void
    {
        $provider = self::mock(ServiceProviderInterface::class);
        $provider->expects('register')->with($this->container);

        $result = $this->container->register($provider);

        self::assertSame($this->container, $result);
    }

    /**
     * Test case for container factory method.
     * The callable should be added into factories array.
     */
    public function testFactoryMethod(): void
    {
        $callable = static function (Container $container) {
            return new \stdClass();
        };

        $this->container->factory('test', $callable);

        // By getting 'test' service from container,
        // we indirectly test that the factory has correctly been stored
        $service = $this->container->get('test');

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
        $this->container->get('non_existent_service');
    }

}