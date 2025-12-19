<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCPaginatorBundle\Param\PaginatorParamInterface;
use Tourze\SpecialOrderBundle\Param\GetOrderOfferChanceListParam;

/**
 * @internal
 */
#[CoversClass(GetOrderOfferChanceListParam::class)]
final class GetOrderOfferChanceListParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetOrderOfferChanceListParam();

        $this->assertInstanceOf(PaginatorParamInterface::class, $param);
    }
}
