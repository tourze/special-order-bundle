<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use OrderCoreBundle\Exception\SkuNotFoundException;
use OrderCoreBundle\Exception\UnsupportedResourceTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\SpecialOrderBundle\Entity\OfferChance;
use Tourze\SpecialOrderBundle\Entity\OfferSku;
use Tourze\SpecialOrderBundle\Service\SpuOfferResourceProvider;

/**
 * @internal
 */
#[CoversClass(SpuOfferResourceProvider::class)]
#[RunTestsInSeparateProcesses]
final class SpuOfferResourceProviderTest extends AbstractIntegrationTestCase
{
    private SpuOfferResourceProvider $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(SpuOfferResourceProvider::class);
    }

    public function testServiceExists(): void
    {
        $this->assertInstanceOf(SpuOfferResourceProvider::class, $this->service);
    }

    public function testSendResourceCreatesOfferChanceAndOfferSku(): void
    {
        // 创建测试用户
        $user = $this->createNormalUser('test_user_' . uniqid(), 'password');

        // 创建测试 Spu
        $realSpu = new Spu();
        $realSpu->setTitle('测试商品');
        $realSpu->setValid(true);
        $this->persistAndFlush($realSpu);

        // 创建测试 Sku并关联到Spu
        $sku = new Sku();
        $sku->setTitle('测试SKU');
        $sku->setUnit('个');
        $sku->setNeedConsignee(false);
        $sku->setValid(true);
        $sku->setSpu($realSpu);
        $realSpu->addSku($sku);
        $this->persistAndFlush($sku);

        // 使用真实的 Spu
        $spu = $realSpu;

        // 测试数量
        $amount = '5';

        // 调用 sendResource 方法
        $this->service->sendResource($user, $spu, $amount);

        // 验证创建了 OfferChance
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        /** @phpstan-ignore doctrine.noGetRepositoryOutsideService */
        $offerChances = $entityManager
            ->getRepository(OfferChance::class)
            ->findBy(['user' => $user])
        ;

        $this->assertCount(1, $offerChances, '应该创建一个 OfferChance');
        $offerChance = $offerChances[0];

        $this->assertEquals('获得实物测试商品', $offerChance->getTitle());
        $this->assertEquals($user, $offerChance->getUser());
        $this->assertTrue($offerChance->getValid());
        $this->assertNotNull($offerChance->getStartTime());

        // 验证创建了 OfferSku
        /** @phpstan-ignore doctrine.noGetRepositoryOutsideService */
        $offerSkus = $entityManager
            ->getRepository(OfferSku::class)
            ->findBy(['chance' => $offerChance])
        ;

        $this->assertCount(1, $offerSkus, '应该创建一个 OfferSku');
        $offerSku = $offerSkus[0];

        $this->assertEquals($offerChance, $offerSku->getChance());
        $this->assertEquals($sku, $offerSku->getSku());
        $this->assertEquals(5, $offerSku->getQuantity());
    }

    public function testSendResourceThrowsExceptionForInvalidResourceType(): void
    {
        // 创建测试用户
        $user = $this->createNormalUser('test_user_' . uniqid(), 'password');

        // 创建非 Spu 类型的资源身份
        $invalidResource = new MockResourceIdentity('invalid-id', 'Invalid Resource');

        // 验证抛出 UnsupportedResourceTypeException 异常
        $this->expectException(UnsupportedResourceTypeException::class);
        $this->expectExceptionMessage('此资源提供者仅支持SPU类型的资源');

        $this->service->sendResource($user, $invalidResource, '1');
    }

    public function testSendResourceThrowsExceptionForInvalidSpu(): void
    {
        // 创建测试用户
        $user = $this->createNormalUser('test_user_' . uniqid(), 'password');

        // 创建没有 Sku 的真实 Spu
        $emptySpu = new Spu();
        $emptySpu->setTitle('空SPU');
        $emptySpu->setValid(true);
        $this->persistAndFlush($emptySpu);

        // 验证抛出 SkuNotFoundException 异常
        $this->expectException(SkuNotFoundException::class);
        $this->expectExceptionMessage('找不到SPU关联的SKU信息');

        $this->service->sendResource($user, $emptySpu, '1');
    }

    public function testSendResourceWithExpireTime(): void
    {
        // 创建测试用户
        $user = $this->createNormalUser('test_user_' . uniqid(), 'password');

        // 创建测试 Spu
        $realSpu = new Spu();
        $realSpu->setTitle('测试商品');
        $realSpu->setValid(true);
        $this->persistAndFlush($realSpu);

        // 创建测试 Sku并关联到Spu
        $sku = new Sku();
        $sku->setTitle('测试SKU');
        $sku->setUnit('个');
        $sku->setNeedConsignee(false);
        $sku->setValid(true);
        $sku->setSpu($realSpu);
        $realSpu->addSku($sku);
        $this->persistAndFlush($sku);

        // 使用真实的 Spu
        $spu = $realSpu;

        // 设置过期时间
        $expireTime = new \DateTimeImmutable('+7 days');

        // 调用 sendResource 方法
        $this->service->sendResource($user, $spu, '3', null, $expireTime);

        // 验证过期时间设置正确
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        /** @phpstan-ignore doctrine.noGetRepositoryOutsideService */
        $offerChances = $entityManager
            ->getRepository(OfferChance::class)
            ->findBy(['user' => $user])
        ;

        $this->assertCount(1, $offerChances);
        $offerChance = $offerChances[0];

        $this->assertEquals($expireTime, $offerChance->getEndTime());
    }

    public function testFindIdentityMethodExistsAndCallsSpuService(): void
    {
        // 验证 findIdentity 方法存在且为 public
        $reflection = new \ReflectionMethod($this->service, 'findIdentity');
        $this->assertTrue($reflection->isPublic());

        // 验证方法参数
        $parameters = $reflection->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertEquals('identity', $parameters[0]->getName());
        $type = $parameters[0]->getType();
        $this->assertEquals('string', $type instanceof \ReflectionNamedType ? $type->getName() : (string) $type);

        // 验证该方法会调用 SpuService（通过直接调用测试）
        $result = $this->service->findIdentity('non-existent-id');
        // 对于不存在的 ID，应该返回 null（由 SpuService 返回）
        $this->assertNull($result);
    }

    public function testGetIdentitiesMethodExistsAndCallsSpuService(): void
    {
        // 验证 getIdentities 方法存在且为 public
        $reflection = new \ReflectionMethod($this->service, 'getIdentities');
        $this->assertTrue($reflection->isPublic());

        // 验证方法没有参数
        $parameters = $reflection->getParameters();
        $this->assertCount(0, $parameters);

        // 调用方法验证其能正常执行（具体的数据依赖于实际的 SpuService 实现）
        $result = $this->service->getIdentities();
        // 结果符合返回类型约定 ?iterable
        $this->assertTrue(null === $result || $result instanceof \Iterator || is_array($result), 'getIdentities方法应该返回null或者可迭代对象');
    }

    public function testGetCodeReturnsCorrectValue(): void
    {
        $this->assertEquals('material', $this->service->getCode());
    }

    public function testGetLabelReturnsCorrectValue(): void
    {
        $this->assertEquals('实物奖(SPU)', $this->service->getLabel());
    }

    public function testPrivateMethodsReflection(): void
    {
        // 验证 getFirstSku 私有方法存在
        $reflection = new \ReflectionMethod($this->service, 'getFirstSku');
        $this->assertTrue($reflection->isPrivate());

        // 验证 getSpuResourceLabel 私有方法存在
        $reflection = new \ReflectionMethod($this->service, 'getSpuResourceLabel');
        $this->assertTrue($reflection->isPrivate());

        // 测试 getSpuResourceLabel 的默认行为
        $reflection->setAccessible(true);
        $emptySpu = new Spu();
        $emptySpu->setTitle('测试SPU标题');
        $result = $reflection->invoke($this->service, $emptySpu);
        $this->assertEquals('测试SPU标题', $result);
    }
}
