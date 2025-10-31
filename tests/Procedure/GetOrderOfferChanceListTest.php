<?php

declare(strict_types=1);

namespace Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\SpecialOrderBundle\Entity\OfferChance;
use Tourze\SpecialOrderBundle\Procedure\GetOrderOfferChanceList;

/**
 * @internal
 */
#[CoversClass(GetOrderOfferChanceList::class)]
#[RunTestsInSeparateProcesses]
final class GetOrderOfferChanceListTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 该测试类不需要额外的设置
    }

    public function testFormatItemShouldReturnFormattedArray(): void
    {
        $procedure = $this->getMockBuilder(GetOrderOfferChanceList::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $offerChance = $this->createMock(OfferChance::class);

        $offerChance->expects($this->once())
            ->method('getId')
            ->willReturn('offer123')
        ;

        $offerChance->expects($this->once())
            ->method('getTitle')
            ->willReturn('Special Offer Title')
        ;

        $reflectionMethod = new \ReflectionMethod(GetOrderOfferChanceList::class, 'formatItem');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invoke($procedure, $offerChance);

        $expected = [
            'id' => 'offer123',
            'title' => 'Special Offer Title',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testCanBeInstantiated(): void
    {
        $procedure = $this->getMockBuilder(GetOrderOfferChanceList::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->assertInstanceOf(GetOrderOfferChanceList::class, $procedure);
    }

    public function testExecute(): void
    {
        $procedure = self::getService(GetOrderOfferChanceList::class);
        $this->assertTrue(method_exists($procedure, 'execute'));
    }
}
