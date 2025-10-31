<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle;

use BenefitBundle\BenefitBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use OrderCoreBundle\OrderCoreBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\JsonRPCSecurityBundle\JsonRPCSecurityBundle;
use Tourze\ProductCoreBundle\ProductCoreBundle;

class SpecialOrderBundle extends Bundle implements BundleDependencyInterface
{
    /**
     * @return array<class-string<Bundle>, array<string, bool>>
     */
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            OrderCoreBundle::class => ['all' => true],
            SecurityBundle::class => ['all' => true],
            JsonRPCSecurityBundle::class => ['all' => true],
            BenefitBundle::class => ['all' => true],
            ProductCoreBundle::class => ['all' => true],
        ];
    }
}
