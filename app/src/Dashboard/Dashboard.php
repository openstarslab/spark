<?php

namespace Spark\Dashboard;

use Nulldark\Routing\Route;
use Nulldark\Routing\RouteCollection;
use Spark\Framework\Container\ContainerInterface;
use Spark\Framework\Extension\Extension;
use Spark\Framework\Http\Request;
use Spark\Framework\Http\Response;

final class Dashboard extends Extension
{
    public function register(ContainerInterface $container): void
    {
        $this->loadRoutes(function (RouteCollection $routes) {
            $route = new Route(['GET'], '/test', function (Request $request, Response $response) {
                new Response\HtmlResponse('<h1>Hello world</h1>');
            });

            $routes->add($route);
        });
    }
}
