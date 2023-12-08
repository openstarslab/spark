<?php

try {
    /** @var \Spark\Framework\Foundation\KernelInterface $bootstrap */
    $bootstrap = require_once __DIR__ . '/../app/bootstrap.php';
} catch (\Exception $exc) {
    echo "<p>{$exc->getMessage()}" . PHP_EOL;
    echo "<p>{$exc->getTraceAsString()}</p>" . PHP_EOL;

    exit(1);
}

$bootstrap->start(
    $bootstrap->createApplication(\Spark\Framework\Foundation\Application\Http::class)
);