<?php

namespace Spark\Core\Extension\Loader;

use Composer\Package\CompletePackageInterface;
use Spark\Core\Extension\ExtensionFinder;
use function end;
use function explode;

class FilesystemExtenesionLoader extends ExtensionLoader
{
    public function loadExtensionsInfos(): void
    {
        $extenesions = $this->getExtensionFinder()
            ->loadExtenesions(
                $this->type->getExtensionPath()
            );

        $this->extensionInfos = [];

        foreach ($extenesions as $extenesion) {
            /** @var CompletePackageInterface $package */
            $package = $extenesion['composerPackage'];

            $nameParts = explode('\\', (string)$extenesion['baseClass']);
            $this->extensionInfos[] = [
                'name' => end($nameParts),
                'path' => $extenesion['path'],
                'version' => $package->getPrettyVersion(),
                'baseClass' => $extenesion['baseClass'],
                'active' => true,
                'autoload' => $package->getAutoload()
            ];
        }
    }

    private function getExtensionFinder(): ExtensionFinder
    {
        return new ExtensionFinder($this->root);
    }
}