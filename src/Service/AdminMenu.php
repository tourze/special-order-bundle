<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\SpecialOrderBundle\Controller\Admin\OrderOfferChanceCrudController;
use Tourze\SpecialOrderBundle\Controller\Admin\OrderOfferSkuCrudController;

/**
 * 特殊订单管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('订单管理')) {
            $item->addChild('订单管理');
        }

        $orderMenu = $item->getChild('订单管理');
        if (null === $orderMenu) {
            return;
        }

        // 添加特殊订单管理子菜单
        if (null === $orderMenu->getChild('特殊订单管理')) {
            $orderMenu->addChild('特殊订单管理')
                ->setAttribute('icon', 'fas fa-star')
            ;
        }

        $specialOrderMenu = $orderMenu->getChild('特殊订单管理');
        if (null === $specialOrderMenu) {
            return;
        }

        $specialOrderMenu->addChild('报价机会')
            ->setUri($this->linkGenerator->getCurdListPage(OrderOfferChanceCrudController::class))
            ->setAttribute('icon', 'fas fa-handshake')
        ;

        $specialOrderMenu->addChild('机会SKU')
            ->setUri($this->linkGenerator->getCurdListPage(OrderOfferSkuCrudController::class))
            ->setAttribute('icon', 'fas fa-cube')
        ;
    }
}
