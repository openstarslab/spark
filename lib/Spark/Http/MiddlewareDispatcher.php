<?php

namespace Spark\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spark\Http\Middleware\RequestHandler;
use Spark\Routing\RouteRunner;

/**
 * Class MiddlewareDispatcher
 *
 * MiddlewareDispatcher is a final class that implements the RequestHandlerInterface.
 * It is responsible for dispatching the request to a chain of middlewares.
 */
final class MiddlewareDispatcher implements RequestHandlerInterface
{
    protected RequestHandlerInterface $next;

    public function __construct(MiddlewareInterface ...$middlewares)
    {
        $this->next = new RouteRunner();

        foreach ($middlewares as $middleware) {
            $this->add($middleware);
        }
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->next->handle($request);
    }

    /**
     * Adds a middleware to the stack.
     *
     * @param MiddlewareInterface $middleware
     *  The middleware to be added.
     *
     * @return self
     *  Returns the instance of the class on which the method is called.
     */
    public function add(MiddlewareInterface $middleware): self
    {
        $next = $this->next;

        $this->next = new class ($middleware, $next) implements RequestHandlerInterface {
            public function __construct(
                private readonly MiddlewareInterface     $middleware,
                private readonly RequestHandlerInterface $next
            ) {
            }

            /**
             * @inheritDoc
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->middleware->process($request, $this->next);
            }
        };

        return $this;
    }
}