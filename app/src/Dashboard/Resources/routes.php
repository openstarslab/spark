<?php

use Spark\Framework\Http\Request;
use Spark\Framework\Http\Response;

return function (\Nulldark\Routing\RouteCollection $routeCollection): void {
    $route = new \Nulldark\Routing\Route(['GET'], '/', function (Request $request, Response $response) {
        $response->getBody()->write('<h1>Hello world</h1>');

        return $response->withHeader('Content-Type', 'text/html');
    });

    $routeCollection->add($route);
};
