<?php

namespace Spark\Core\Extension\Loader;

use Spark\Core\Extension\ExtensionInterface;

interface ExtenesionLoaderInterface
{
    /**
     * Gets all initialized extenesions.
     *
     * @return array
     */
    public function getExtensionInstances(): array;

    /**
     * Gets extenesion instance via given name.
     *
     * @param string $name
     *  Extension name.
     *
     * @return ExtensionInterface|null
     */
    public function getExtensionInstance(string $name): ?ExtensionInterface;

    /**
     * Initializes all extenesions.
     *
     * @return void
     */
    public function initializeExtenesions(): void;
}