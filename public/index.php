<?php

try {
    /** @var \Spark\Framework\App\Spark $kernel */
    $kernel = require_once __DIR__ . '/../app/bootstrap.php';
} catch (\Exception $exc) {
    echo "<p>{$exc->getMessage()}" . PHP_EOL;
    echo "<p>{$exc->getTraceAsString()}</p>" . PHP_EOL;

    exit(1);
}

$kernel->boot();
$kernel->start(
    $kernel->createApplication(\Spark\Framework\App\Application\Http::class)
);

exit(0);