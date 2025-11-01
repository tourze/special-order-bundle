<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\SpecialOrderBundle\Controller\Admin\OrderOfferSkuCrudController;

/**
 * @internal
 */
#[CoversClass(OrderOfferSkuCrudController::class)]
#[RunTestsInSeparateProcesses]
final class OrderOfferSkuCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testIndex(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin/order/offer-sku');

        // 验证响应状态码
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isSuccessful());

        // 验证页面基本结构
        $crawler = $client->getCrawler();
        $this->assertGreaterThan(0, $crawler->filter('body')->count());
        $this->assertGreaterThan(0, $crawler->filter('title')->count());
    }

    /**
     * @return OrderOfferSkuCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(OrderOfferSkuCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'chance' => ['机会'];
        yield 'sku' => ['SKU'];
        yield 'quantity' => ['数量'];
        yield 'price' => ['价格'];
        yield 'currency' => ['币种'];
        yield 'createTime' => ['创建时间'];
        yield 'updateTime' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'quantity' => ['quantity'];
        yield 'price' => ['price'];
        yield 'currency' => ['currency'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'quantity' => ['quantity'];
        yield 'price' => ['price'];
        yield 'currency' => ['currency'];
    }

    /**
     * 重写抽象基类的硬编码字段验证，适配当前实体的实际字段
     */
}
