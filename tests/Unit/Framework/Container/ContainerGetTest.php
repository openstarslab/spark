<?php

namespace Spark\Tests\Unit\Framework\Container;

use PHPUnit\Framework\Attributes\CoversClass;
use Spark\Framework\Container\Container;
use Spark\Framework\Container\ServiceProviderInterface;

#[CoversClass(Container::class)]
class ContainerGetTest extends \Spark\Tests\Unit\TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();
    }

    /**
     * Test if set method stores correctly the key-value pair in the container
     */
    public function testSetStoresKeyValueInContainer(): void
    {
        $this->container->set('name', 'Test Framework');

        self::assertEquals('Test Framework', $this->container->get('name'));
    }

    /**
     * Test the `set` method with different types of values.
     */
    public function testSetWithDifferentValues(): void
    {
        $this->container->set('string', 'string');
        $this->container->set('integer', 1);
        $this->container->set('float', 1.23);
        $this->container->set('array', ['element']);

        self::assertSame('string', $this->container->get('string'));
        self::assertSame(1, $this->container->get('integer'));
        self::assertSame(1.23, $this->container->get('float'));
        self::assertSame(['element'], $this->container->get('array'));
    }

    public function testRegisterMethod(): void
    {
        $provider = self::mock(ServiceProviderInterface::class);
        $provider->expects('register')->with($this->container);

        $result = $this->container->register($provider);

        self::assertSame($this->container, $result);
    }
}
