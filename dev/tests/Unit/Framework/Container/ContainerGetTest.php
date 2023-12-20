<?php

namespace Spark\Tests\Unit\Framework\Container;

use PHPUnit\Framework\Attributes\CoversClass;
use Spark\Framework\Container\Container;
use Spark\Framework\Container\ServiceProviderInterface;

#[CoversClass(Container::class)]
class ContainerGetTest extends \Spark\Tests\Unit\TestCase
{
    private Container $container;

    /**
     * Test if set method stores correctly the key-value pair in the container
     */
    public function testSetStoresKeyValueInContainer(): void
    {
        $this->container->setParameter('name', 'Test Framework');

        self::assertEquals('Test Framework', $this->container->getParameter('name'));
    }

    /**
     * Test the `set` method with different types of values.
     */
    public function testSetWithDifferentValues(): void
    {
        $this->container->setParameter('string', 'string');
        $this->container->setParameter('integer', 1);
        $this->container->setParameter('float', 1.23);
        $this->container->setParameter('array', ['element']);

        self::assertSame('string', $this->container->getParameter('string'));
        self::assertSame(1, $this->container->getParameter('integer'));
        self::assertSame(1.23, $this->container->getParameter('float'));
        self::assertSame(['element'], $this->container->getParameter('array'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();
    }
}
