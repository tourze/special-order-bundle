<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle\Tests\Repository;

use BizUserBundle\Entity\BizUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\SpecialOrderBundle\Entity\OfferChance;
use Tourze\SpecialOrderBundle\Entity\OfferSku;
use Tourze\SpecialOrderBundle\Repository\OfferSkuRepository;

/**
 * @internal
 */
#[CoversClass(OfferSkuRepository::class)]
#[RunTestsInSeparateProcesses]
final class OfferSkuRepositoryTest extends AbstractRepositoryTestCase
{
    private OfferSkuRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(OfferSkuRepository::class);

        // 创建一些测试数据以确保count测试通过
        $testEntity = $this->createNewEntity();
        $this->persistAndFlush($testEntity);
    }

    protected function createNewEntity(): OfferSku
    {
        // 创建关联的OfferChance实体
        $chance = new OfferChance();
        $chance->setTitle('Test Offer Chance ' . uniqid());
        $chance->setValid(true);

        // 创建一个测试用户实体
        $testUser = new BizUser();
        $testUser->setUsername('test_user_' . uniqid());
        $testUser->setNickName('Test User');
        $testUser->setPlainPassword('test_password');
        $testUser->setValid(true);

        self::getEntityManager()->persist($testUser);
        self::getEntityManager()->flush();

        $chance->setUser($testUser);

        self::getEntityManager()->persist($chance);

        // 创建关联的SKU实体
        $sku = new Sku();
        $sku->setUnit('piece');
        $sku->setNeedConsignee(false);
        $sku->setValid(true);

        // 创建关联的SPU实体
        $spu = new Spu();
        $spu->setTitle('Test Product ' . uniqid());
        $spu->setValid(true);

        self::getEntityManager()->persist($spu);
        $sku->setSpu($spu);
        self::getEntityManager()->persist($sku);

        self::getEntityManager()->flush();

        // 创建OfferSku实体
        $entity = new OfferSku();
        $entity->setChance($chance);
        $entity->setSku($sku);
        $entity->setQuantity(1);
        $entity->setPrice('99.99');
        $entity->setCurrency('CNY');

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<OfferSku>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    public function testClearClearsEntityManager(): void
    {
        // 创建一个实体并持久化
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 验证实体在EntityManager中
        $this->assertTrue(self::getEntityManager()->contains($entity), '实体应该在EntityManager中');

        // 执行clear操作
        $this->repository->clear();

        // 验证实体已被清除
        $this->assertFalse(self::getEntityManager()->contains($entity), '清除操作后，实体应该不再在EntityManager中');
    }

    public function testFlushFlushesChanges(): void
    {
        // 创建一个实体并持久化但不刷新
        $entity = $this->createNewEntity();
        self::getEntityManager()->persist($entity);

        // 记录flush前的数据库记录数量
        $countBeforeFlush = $this->repository->count();

        // 执行flush操作
        $this->repository->flush();

        // 验证flush后数据库记录数量增加了1
        $countAfterFlush = $this->repository->count();
        $this->assertEquals($countBeforeFlush + 1, $countAfterFlush, '刷新后数据库记录数应该增加1');

        // 验证实体有ID并且能从数据库中查询到
        $id = self::getEntityManager()->getUnitOfWork()->getSingleIdentifierValue($entity);
        $this->assertNotNull($id, '实体应该有ID');

        $foundEntity = $this->repository->find($id);
        $this->assertInstanceOf($this->repository->getClassName(), $foundEntity, '刷新后实体应该能从数据库中查询到');
    }

    public function testSaveAllPersistsEntities(): void
    {
        $entities = [$this->createNewEntity(), $this->createNewEntity()];

        // 批量保存
        $this->repository->saveAll($entities);

        // 验证所有实体都已被持久化
        foreach ($entities as $entity) {
            $id = self::getEntityManager()->getUnitOfWork()->getSingleIdentifierValue($entity);
            $this->assertNotNull($id, '批量保存后实体应该有ID');

            // 从数据库查询验证实体确实被保存
            $foundEntity = $this->repository->find($id);
            $this->assertInstanceOf($this->repository->getClassName(), $foundEntity, '保存的实体应该能从数据库中查询到');
        }
    }
}
