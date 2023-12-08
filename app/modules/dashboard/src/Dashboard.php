<?php

namespace Spark\Dashboard;


use Spark\Framework\DependencyInjection\Builder\ContainerBuilderInterface;

final class Dashboard extends \Spark\System\Module\Module
{
    public function register(ContainerBuilderInterface $container): void
    {
        parent::register($container);
    }

    public function boot(): void
    {

    }
}