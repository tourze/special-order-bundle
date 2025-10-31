<?php

namespace Tourze\SpecialOrderBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\SpecialOrderBundle\Repository\OfferSkuRepository;

#[ORM\Table(name: 'order_offer_sku', options: ['comment' => '机会SKU'])]
#[ORM\UniqueConstraint(name: 'order_offer_sku_idx_uniq', columns: ['chance_id', 'sku_id'])]
#[ORM\Entity(repositoryClass: OfferSkuRepository::class)]
class OfferSku implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;

    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'skus')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[NotNull]
    private ?OfferChance $chance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[NotNull]
    private Sku $sku;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => '1', 'comment' => '数量'])]
    #[Positive]
    private int $quantity = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00', 'comment' => '价格'])]
    #[PositiveOrZero]
    #[Length(max: 10)]
    private string $price = '0.00';

    #[ORM\Column(type: Types::STRING, length: 10, options: ['default' => 'CNY', 'comment' => '币种'])]
    #[NotBlank]
    #[Length(max: 10)]
    private string $currency = 'CNY';

    public function __toString(): string
    {
        return $this->sku->__toString();
    }

    public function getChance(): ?OfferChance
    {
        return $this->chance;
    }

    public function setChance(?OfferChance $chance): void
    {
        $this->chance = $chance;
    }

    public function getSku(): Sku
    {
        return $this->sku;
    }

    public function setSku(Sku $sku): void
    {
        $this->sku = $sku;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }
}
