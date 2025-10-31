<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use OrderCoreBundle\OrderCoreBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\SpecialOrderBundle\SpecialOrderBundle;

/**
 * @internal
 */
#[CoversClass(SpecialOrderBundle::class)]
#[RunTestsInSeparateProcesses]
final class SpecialOrderBundleTest extends AbstractBundleTestCase
{
    public function testBundleDependencies(): void
    {
        $dependencies = SpecialOrderBundle::getBundleDependencies();

        $this->assertArrayHasKey(DoctrineBundle::class, $dependencies);
        $this->assertArrayHasKey(OrderCoreBundle::class, $dependencies);
        $this->assertArrayHasKey(SecurityBundle::class, $dependencies);

        $this->assertEquals(['all' => true], $dependencies[DoctrineBundle::class]);
        $this->assertEquals(['all' => true], $dependencies[OrderCoreBundle::class]);
        $this->assertEquals(['all' => true], $dependencies[SecurityBundle::class]);
    }
}
