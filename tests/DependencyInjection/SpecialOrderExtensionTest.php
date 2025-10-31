<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\SpecialOrderBundle\DependencyInjection\SpecialOrderExtension;

/**
 * @internal
 */
#[CoversClass(SpecialOrderExtension::class)]
final class SpecialOrderExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testExtensionCanLoadConfiguration(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');
        $extension = new SpecialOrderExtension();

        $extension->load([], $container);

        $this->assertInstanceOf(ExtensionInterface::class, $extension);
        $this->assertInstanceOf(ContainerBuilder::class, $container);
    }

    public function testGetAlias(): void
    {
        $extension = new SpecialOrderExtension();
        $this->assertEquals('special_order', $extension->getAlias());
    }

    /**
     * @return array<string, mixed>
     */
    protected function getMinimalConfiguration(): array
    {
        return [];
    }

    protected function getExtension(): ExtensionInterface
    {
        return new SpecialOrderExtension();
    }
}
