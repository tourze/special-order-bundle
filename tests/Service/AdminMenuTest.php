<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use Tourze\SpecialOrderBundle\Service\AdminMenu;

/**
 * AdminMenu服务测试
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // Setup for AdminMenu tests
    }

    public function testInvokeAddsMenuItems(): void
    {
        $container = self::getContainer();
        $adminMenu = $container->get(AdminMenu::class);
        self::assertInstanceOf(AdminMenu::class, $adminMenu);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        // 验证菜单结构
        $orderMenu = $rootItem->getChild('订单管理');
        self::assertNotNull($orderMenu);

        $specialOrderMenu = $orderMenu->getChild('特殊订单管理');
        self::assertNotNull($specialOrderMenu);

        // 验证子菜单项
        $offerChanceItem = $specialOrderMenu->getChild('报价机会');
        self::assertNotNull($offerChanceItem);
        self::assertEquals('fas fa-handshake', $offerChanceItem->getAttribute('icon'));

        $offerSkuItem = $specialOrderMenu->getChild('机会SKU');
        self::assertNotNull($offerSkuItem);
        self::assertEquals('fas fa-cube', $offerSkuItem->getAttribute('icon'));
    }

    public function testInvokeWithExistingOrderMenu(): void
    {
        $container = self::getContainer();
        $adminMenu = $container->get(AdminMenu::class);
        self::assertInstanceOf(AdminMenu::class, $adminMenu);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        // 预先创建订单管理菜单
        $rootItem->addChild('订单管理');

        $adminMenu->__invoke($rootItem);

        // 验证菜单结构
        $orderMenu = $rootItem->getChild('订单管理');
        self::assertNotNull($orderMenu);

        $specialOrderMenu = $orderMenu->getChild('特殊订单管理');
        self::assertNotNull($specialOrderMenu);

        self::assertNotNull($specialOrderMenu->getChild('报价机会'));
        self::assertNotNull($specialOrderMenu->getChild('机会SKU'));
    }

    public function testInvokeWithExistingSpecialOrderMenu(): void
    {
        $container = self::getContainer();
        $adminMenu = $container->get(AdminMenu::class);
        self::assertInstanceOf(AdminMenu::class, $adminMenu);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        // 预先创建菜单结构
        $orderMenu = $rootItem->addChild('订单管理');
        $orderMenu->addChild('特殊订单管理');

        $adminMenu->__invoke($rootItem);

        // 验证子菜单添加成功
        $specialOrderMenu = $orderMenu->getChild('特殊订单管理');
        self::assertNotNull($specialOrderMenu);

        self::assertNotNull($specialOrderMenu->getChild('报价机会'));
        self::assertNotNull($specialOrderMenu->getChild('机会SKU'));
    }

    public function testInvokeHandlesMissingOrderMenu(): void
    {
        $container = self::getContainer();
        $adminMenu = $container->get(AdminMenu::class);
        self::assertInstanceOf(AdminMenu::class, $adminMenu);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        // 模拟订单管理菜单不存在的情况
        $adminMenu->__invoke($rootItem);

        // 应该创建完整的菜单结构
        $orderMenu = $rootItem->getChild('订单管理');
        self::assertNotNull($orderMenu);

        $specialOrderMenu = $orderMenu->getChild('特殊订单管理');
        self::assertNotNull($specialOrderMenu);

        self::assertNotNull($specialOrderMenu->getChild('报价机会'));
        self::assertNotNull($specialOrderMenu->getChild('机会SKU'));
    }
}
