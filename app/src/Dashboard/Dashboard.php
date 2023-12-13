<?php

namespace Spark\Dashboard;

use Nulldark\Routing\RouteCollection;
use Spark\Framework\Container\ContainerInterface;
use Spark\Framework\Extension\Extension;

final class Dashboard extends Extension
{
    public function register(ContainerInterface $container): void
    {
        $this->loadRoutes(function (RouteCollection $routes) {
        });
    }
}
