<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;
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

    public function testCanBeInstantiated(): void
    {
        $procedure = self::getService(GetOrderOfferChanceList::class);
        $this->assertInstanceOf(GetOrderOfferChanceList::class, $procedure);
    }

    public function testExecuteMethodExists(): void
    {
        $procedure = self::getService(GetOrderOfferChanceList::class);

        $reflection = new \ReflectionMethod($procedure, 'execute');
        $this->assertTrue($reflection->isPublic());

        $parameters = $reflection->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertEquals('param', $parameters[0]->getName());
    }

    public function testFormatItemReturnsCorrectStructure(): void
    {
        $procedure = self::getService(GetOrderOfferChanceList::class);

        // 创建真实的 OfferChance 实体
        $user = $this->createNormalUser('test_user_' . uniqid(), 'password');

        $offerChance = new OfferChance();
        $offerChance->setTitle('Test Offer Title');
        $offerChance->setUser($user);
        $offerChance->setStartTime(new \DateTimeImmutable());
        $offerChance->setValid(true);

        $this->persistAndFlush($offerChance);

        // 通过反射调用私有方法
        $reflection = new \ReflectionMethod($procedure, 'formatItem');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($procedure, $offerChance);

        // 验证返回结构
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertEquals('Test Offer Title', $result['title']);
    }
}
