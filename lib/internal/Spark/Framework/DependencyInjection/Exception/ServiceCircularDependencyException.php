<?php

namespace Spark\Framework\DependencyInjection\Exception;

class ServiceCircularDependencyException extends \RuntimeException
{
    public function __construct(string $id, array $services = [])
    {
        var_dump($services);
        parent::__construct(
            sprintf('Circular dependency detected while trying to resolve entry "%s", path: "%s"',
                $id, \implode(' -> ', $services)
            )
        );
    }
}