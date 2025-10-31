<?php

namespace Tourze\SpecialOrderBundle\Procedure;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Domain\JsonRpcMethodInterface;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;
use Tourze\SpecialOrderBundle\Entity\OfferChance;
use Tourze\SpecialOrderBundle\Repository\OfferChanceRepository;

#[MethodTag(name: '特惠机会')]
#[MethodDoc(summary: '获取用户的特惠机会')]
#[MethodExpose(method: 'GetOrderOfferChanceList')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetOrderOfferChanceList extends BaseProcedure implements JsonRpcMethodInterface
{
    use PaginatorTrait;

    public function __construct(
        private readonly Security $security,
        private readonly OfferChanceRepository $offerChanceRepository,
    ) {
    }

    public function execute(): array
    {
        $qb = $this->offerChanceRepository
            ->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.valid = :valid')
            ->setParameter('user', $this->security->getUser())
            ->setParameter('valid', true)
        ;

        return $this->fetchList($qb, $this->formatItem(...));
    }

    /**
     * @return array<string, mixed>
     */
    private function formatItem(OfferChance $item): array
    {
        return [
            'id' => $item->getId(),
            'title' => $item->getTitle(),
        ];
    }
}
