<?php

namespace Tourze\SpecialOrderBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineResolveTargetEntityBundle\Service\ResolveTargetEntityService;
use Tourze\SpecialOrderBundle\Entity\OfferChance;

#[When(env: 'test')]
#[When(env: 'dev')]
class OfferChanceFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private readonly ResolveTargetEntityService $resolveTargetEntityService,
    ) {
    }

    public static function getGroups(): array
    {
        return ['order', 'test'];
    }

    public function load(ObjectManager $manager): void
    {
        // 获取用户实体类
        /** @var class-string<UserInterface> $userClass */
        $userClass = $this->resolveTargetEntityService->findEntityClass(UserInterface::class);

        // 创建测试用户
        $testUser = new $userClass();
        if (method_exists($testUser, 'setUsername')) {
            $testUser->setUsername('offer_test_user');
        }
        if (method_exists($testUser, 'setNickName')) {
            $testUser->setNickName('报价测试用户');
        }
        if (method_exists($testUser, 'setValid')) {
            $testUser->setValid(true);
        }
        $manager->persist($testUser);

        $offerChanceData = [
            [
                'title' => '春季促销商品报价机会',
                'description' => '针对春季热销商品的特殊报价机会，数量有限',
                'startAt' => new \DateTimeImmutable('2025-03-01 00:00:00'),
                'endAt' => new \DateTimeImmutable('2025-03-31 23:59:59'),
                'isActive' => true,
                'priority' => 1,
            ],
            [
                'title' => '批量采购优惠报价',
                'description' => '大客户批量采购专享报价机会',
                'startAt' => new \DateTimeImmutable('2025-02-01 00:00:00'),
                'endAt' => new \DateTimeImmutable('2025-04-30 23:59:59'),
                'isActive' => true,
                'priority' => 2,
            ],
            [
                'title' => '新品试用报价',
                'description' => '新产品推广期间的特殊报价机会',
                'startAt' => new \DateTimeImmutable('2025-01-15 00:00:00'),
                'endAt' => new \DateTimeImmutable('2025-02-15 23:59:59'),
                'isActive' => false,
                'priority' => 3,
            ],
            [
                'title' => '年末清仓报价',
                'description' => '年末库存清理专项报价机会',
                'startAt' => new \DateTimeImmutable('2024-12-01 00:00:00'),
                'endAt' => new \DateTimeImmutable('2024-12-31 23:59:59'),
                'isActive' => false,
                'priority' => 4,
            ],
        ];

        foreach ($offerChanceData as $index => $data) {
            $offerChance = new OfferChance();
            $offerChance->setTitle($data['title']);
            // $offerChance->setDescription($data['description']); // Description method not available
            $offerChance->setStartTime($data['startAt']);
            $offerChance->setEndTime($data['endAt']);
            $offerChance->setValid($data['isActive']);
            // $offerChance->setPriority($data['priority']); // Priority method not available

            // 设置创建用户
            assert($testUser instanceof UserInterface);
            $offerChance->setCreatedBy($testUser->getUserIdentifier());

            // 设置用户关联
            $offerChance->setUser($testUser);

            $manager->persist($offerChance);
            $this->addReference('offer-chance-' . $index, $offerChance);
        }

        $manager->flush();
    }
}
