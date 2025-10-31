<?php

declare(strict_types=1);

namespace EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;
use Tourze\SpecialOrderBundle\EventSubscriber\OfferChanceSubscriber;

/**
 * @internal
 */
#[CoversClass(OfferChanceSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class OfferChanceSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // 该测试类不需要额外的设置
    }

    public function testCanBeInstantiated(): void
    {
        $subscriber = self::getService(OfferChanceSubscriber::class);
        $this->assertInstanceOf(OfferChanceSubscriber::class, $subscriber);
    }

    public function testHasEventListenerMethod(): void
    {
        $subscriber = self::getService(OfferChanceSubscriber::class);

        // 验证事件监听方法存在
        $this->assertTrue(method_exists($subscriber, 'updateOfferChanceContract'));
    }

    public function testServiceIsRegistered(): void
    {
        $subscriber = self::getService(OfferChanceSubscriber::class);
        $this->assertInstanceOf(OfferChanceSubscriber::class, $subscriber);
    }

    public function testUpdateOfferChanceContract(): void
    {
        $subscriber = self::getService(OfferChanceSubscriber::class);
        $this->assertTrue(method_exists($subscriber, 'updateOfferChanceContract'));
    }
}
