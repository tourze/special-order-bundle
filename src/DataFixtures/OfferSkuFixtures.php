<?php

namespace Tourze\SpecialOrderBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\ProductCoreBundle\DataFixtures\SkuFixtures;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\SpecialOrderBundle\Entity\OfferChance;
use Tourze\SpecialOrderBundle\Entity\OfferSku;

#[When(env: 'dev')]
class OfferSkuFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['order', 'test']; // 添加 'test' 让测试可以加载
    }

    public function getDependencies(): array
    {
        return [OfferChanceFixtures::class, SkuFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $offerSkuData = [
            [
                'originalPrice' => 199.00,
                'offerPrice' => 159.00,
                'minQuantity' => 10,
                'maxQuantity' => 100,
                'isActive' => true,
                'remark' => '春季促销特价，限量供应',
                'offerChanceRef' => 'offer-chance-0',
            ],
            [
                'originalPrice' => 299.00,
                'offerPrice' => 249.00,
                'minQuantity' => 5,
                'maxQuantity' => 50,
                'isActive' => true,
                'remark' => '批量采购优惠价格',
                'offerChanceRef' => 'offer-chance-1',
            ],
            [
                'originalPrice' => 99.00,
                'offerPrice' => 79.00,
                'minQuantity' => 20,
                'maxQuantity' => 200,
                'isActive' => false,
                'remark' => '新品试用期已结束',
                'offerChanceRef' => 'offer-chance-2',
            ],
            [
                'originalPrice' => 149.00,
                'offerPrice' => 99.00,
                'minQuantity' => 1,
                'maxQuantity' => 999,
                'isActive' => false,
                'remark' => '年末清仓价格，已售完',
                'offerChanceRef' => 'offer-chance-3',
            ],
        ];

        foreach ($offerSkuData as $index => $data) {
            $offerSku = new OfferSku();
            // $offerSku->setOriginalPrice($data['originalPrice']); // OriginalPrice method not available
            $offerSku->setPrice((string) $data['offerPrice']);
            $offerSku->setQuantity($data['minQuantity']);
            $offerSku->setSku($this->getReference(SkuFixtures::TEST_SKU_REFERENCE, Sku::class));
            // $offerSku->setMaxQuantity($data['maxQuantity']); // MaxQuantity method not available
            // $offerSku->setIsActive($data['isActive']); // IsActive method not available
            // $offerSku->setRemark($data['remark']); // Remark method not available
            $offerSku->setChance($this->getReference($data['offerChanceRef'], OfferChance::class));

            $manager->persist($offerSku);
            $this->addReference('offer-sku-' . $index, $offerSku);
        }

        $manager->flush();
    }
}
