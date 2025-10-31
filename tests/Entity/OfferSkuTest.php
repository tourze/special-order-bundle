<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\SpecialOrderBundle\Entity\OfferSku;

/**
 * @internal
 */
#[CoversClass(OfferSku::class)]
final class OfferSkuTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new OfferSku();
    }

    public function testCanBeInstantiated(): void
    {
        $entity = new OfferSku();
        $this->assertInstanceOf(OfferSku::class, $entity);
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'quantity' => ['quantity', 5];
        yield 'price' => ['price', '99.99'];
        yield 'currency' => ['currency', 'USD'];
    }
}
