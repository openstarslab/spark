<?php

use Nulldark\Container\ContainerInterface;

class Spark
{
    public const VERSION = '0.1.0';

    protected static null|ContainerInterface $container;

    public static function setContainer(ContainerInterface $container): void
    {
        static::$container = $container;
    }
    public static function getContainer(): ContainerInterface
    {
        if (static::$container === NULL) {
            throw new \RuntimeException('\Spark::$container is not initialized yet. ');
        }

        return static::$container;
    }

    public static function service(string $id): mixed
    {
        return static::getContainer()->get($id);
    }
}