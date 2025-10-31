<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\SpecialOrderBundle\Entity\OfferChance;

/**
 * @internal
 */
#[CoversClass(OfferChance::class)]
final class OfferChanceTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new OfferChance();
    }

    public function testCanBeInstantiated(): void
    {
        $entity = new OfferChance();
        $this->assertInstanceOf(OfferChance::class, $entity);
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'title' => ['title', 'Test Offer'];
        yield 'startTime' => ['startTime', new \DateTimeImmutable('2024-01-01 00:00:00')];
        yield 'endTime' => ['endTime', new \DateTimeImmutable('2024-12-31 23:59:59')];
        yield 'useTime' => ['useTime', new \DateTimeImmutable('2024-06-15 12:00:00')];
        yield 'valid' => ['valid', true];
    }
}
