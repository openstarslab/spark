<?php

namespace Spark\Framework\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class MiddlewareDispatcher
 *
 * MiddlewareDispatcher is a final class that implements the RequestHandlerInterface.
 * It is responsible for dispatching the request to a chain of middlewares.
 */
final class MiddlewareDispatcher implements RequestHandlerInterface
{
    private RequestHandlerInterface $next;

    public function __construct(
        RequestHandlerInterface $finalHandler,
        MiddlewareInterface     ...$middlewares
    )
    {
        $this->next = $finalHandler;

        foreach ($middlewares as $middleware) {
            $this->add($middleware);
        }
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
            )
            {
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

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->next->handle($request);
    }
}
