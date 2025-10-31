<?php

namespace Tourze\SpecialOrderBundle\EventSubscriber;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use OrderCoreBundle\Event\AfterOrderCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Tourze\SpecialOrderBundle\Repository\OfferChanceRepository;
use Yiisoft\Arrays\ArrayHelper;

readonly class OfferChanceSubscriber
{
    public function __construct(
        private OfferChanceRepository $offerChanceRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 下单后更新机会订单，改为无效
     */
    #[AsEventListener]
    public function updateOfferChanceContract(AfterOrderCreatedEvent $event): void
    {
        $offerId = ArrayHelper::getValue($event->getParamList(), 'offerId', 0);
        $offerChance = $this->offerChanceRepository->findOneBy([
            'id' => $offerId,
        ]);
        if (null !== $offerChance) {
            $offerChance->setContract($event->getContract());
            $offerChance->setUseTime(CarbonImmutable::now());
            $offerChance->setValid(false);
            $this->entityManager->persist($offerChance);
            $this->entityManager->flush();
        }
    }
}
