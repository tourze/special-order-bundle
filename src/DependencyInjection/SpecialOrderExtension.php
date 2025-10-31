<?php

namespace Tourze\SpecialOrderBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class SpecialOrderExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
