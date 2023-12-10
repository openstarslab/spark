<?php

namespace Spark\Framework\Foundation\Providers;

use Spark\Framework\Container\ContainerInterface;
use Spark\Framework\Container\ServiceProviderInterface;
use Spark\Framework\Foundation\Application\Http;

class FoundationServiceProvider implements ServiceProviderInterface
{

    public function register(ContainerInterface $container): void
    {
        $container->factory(Http::class, function (ContainerInterface $container) {
            return new Http($container);
        });
    }
}