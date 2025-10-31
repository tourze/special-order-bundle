<?php

namespace Tourze\SpecialOrderBundle\Service;

use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use OrderCoreBundle\Exception\SkuNotFoundException;
use OrderCoreBundle\Exception\UnsupportedResourceTypeException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Service\SpuService;
use Tourze\ResourceManageBundle\Model\ResourceIdentity;
use Tourze\ResourceManageBundle\Service\ResourceProvider;
use Tourze\SpecialOrderBundle\Entity\OfferChance;
use Tourze\SpecialOrderBundle\Entity\OfferSku;

/**
 * TODO 实物奖，直接创建一个免支付的订单？还是说填了地址之后再创建订单
 */
#[Autoconfigure(public: true)]
readonly class SpuOfferResourceProvider implements ResourceProvider
{
    public function __construct(
        private SpuService $spuService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getCode(): string
    {
        return 'material';
    }

    public function getLabel(): string
    {
        return '实物奖(SPU)';
    }

    public function getIdentities(): ?iterable
    {
        return $this->spuService->findAllValidSpus();
    }

    public function findIdentity(string $identity): ?ResourceIdentity
    {
        return $this->spuService->findValidSpuById($identity);
    }

    public function sendResource(UserInterface $user, ResourceIdentity|Spu|null $identity, string $amount, int|float|null $expireDay = null, ?\DateTimeInterface $expireTime = null): void
    {
        if (!$identity instanceof Spu) {
            throw new UnsupportedResourceTypeException('此资源提供者仅支持SPU类型的资源');
        }

        $sku = $this->getFirstSku($identity);
        if (null === $sku) {
            throw new SkuNotFoundException('找不到SPU关联的SKU信息');
        }

        $offerChance = new OfferChance();
        $offerChance->setUser($user);
        $offerChance->setTitle("获得实物{$this->getSpuResourceLabel($identity)}");
        $offerChance->setStartTime(CarbonImmutable::now());
        if (null !== $expireTime) {
            $offerChance->setEndTime($expireTime);
        }
        $offerChance->setValid(true);

        $offerSku = new OfferSku();
        $offerSku->setChance($offerChance);
        $offerSku->setSku($sku);
        $offerSku->setQuantity((int) $amount);

        $this->entityManager->persist($offerChance);
        $this->entityManager->persist($offerSku);
        $this->entityManager->flush();
    }

    /**
     * 安全获取SPU的第一个SKU
     */
    private function getFirstSku(Spu $spu): ?Sku
    {
        try {
            $reflectionClass = new \ReflectionClass($spu);
            if ($reflectionClass->hasMethod('getSkus')) {
                $method = $reflectionClass->getMethod('getSkus');
                $skus = $method->invoke($spu);
                if ($skus instanceof Collection && !$skus->isEmpty()) {
                    $first = $skus->first();

                    return $first instanceof Sku ? $first : null;
                }
            }
        } catch (\ReflectionException $e) {
            // 忽略反射异常
        }

        return null;
    }

    /**
     * 安全获取SPU的资源标签
     */
    private function getSpuResourceLabel(Spu $spu): string
    {
        try {
            $reflectionClass = new \ReflectionClass($spu);
            if ($reflectionClass->hasMethod('getResourceLabel')) {
                $method = $reflectionClass->getMethod('getResourceLabel');
                $result = $method->invoke($spu);
                if (!is_string($result)) {
                    return '实物奖品';
                }

                return $result;
            }
        } catch (\ReflectionException $e) {
            // 忽略反射异常
        }

        return '实物奖品';
    }
}
