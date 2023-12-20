<?php

namespace Spark\Framework\App;

use Spark\Framework\App\Application\Http;
use Spark\Framework\Container\ContainerInterface;
use Spark\Framework\Container\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->app->bind(Http::class, fn(ContainerInterface $container) => new Http(
            $container,
        ));
    }
}
