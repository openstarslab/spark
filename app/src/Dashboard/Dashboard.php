<?php

namespace Spark\Dashboard;

use Nulldark\Routing\Route;
use Nulldark\Routing\RouteCollection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spark\Framework\Container\ContainerInterface;
use Spark\Framework\Extension\Extension;
use Spark\Framework\Http\Response;

final class Dashboard extends Extension
{
    public function register(ContainerInterface $container): void
    {
        $this->loadRoutes(function (RouteCollection $routes) {
        });
    }
}
