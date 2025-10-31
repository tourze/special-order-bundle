<?php

namespace Tourze\SpecialOrderBundle\Entity;

use AntdCpBundle\Builder\Field\DynamicFieldSet;
use BenefitBundle\Model\BenefitResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OrderCoreBundle\Entity\Contract;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\SpecialOrderBundle\Repository\OfferChanceRepository;

/**
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Table(name: 'order_offer_chance', options: ['comment' => '特惠机会'])]
#[ORM\Entity(repositoryClass: OfferChanceRepository::class)]
class OfferChance implements AdminArrayInterface, BenefitResource, \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(length: 255, options: ['comment' => '标题'])]
    #[NotBlank]
    #[Length(max: 255)]
    private ?string $title = null;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[NotNull]
    private ?UserInterface $user = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '生效时间'])]
    #[Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '过期时间'])]
    #[Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $endTime = null;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '使用时间'])]
    #[Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $useTime = null;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否有效', 'default' => 1])]
    #[Type(type: 'bool')]
    private ?bool $valid = true;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Contract $contract = null;

    /**
     * 动态字段集合
     *
     * @DynamicFieldSet()
     *
     * @var Collection<int, OfferSku>
     */
    #[ORM\OneToMany(mappedBy: 'chance', targetEntity: OfferSku::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $skus;

    public function __toString(): string
    {
        return $this->title ?? '';
    }

    public function __construct()
    {
        $this->skus = new ArrayCollection();
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return Collection<int, OfferSku>
     */
    public function getSkus(): Collection
    {
        return $this->skus;
    }

    public function addSku(OfferSku $sku): static
    {
        if (!$this->skus->contains($sku)) {
            $this->skus->add($sku);
            $sku->setChance($this);
        }

        return $this;
    }

    public function removeSku(OfferSku $sku): static
    {
        if ($this->skus->removeElement($sku)) {
            // set the owning side to null (unless already changed)
            if ($sku->getChance() === $this) {
                $sku->setChance(null);
            }
        }

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'useTime' => $this->getUseTime()?->format('Y-m-d H:i:s'),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->valid,
            'contract' => $this->getContract(),
            '__deletable' => null === $this->getContract(),
        ];
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getUseTime(): ?\DateTimeInterface
    {
        return $this->useTime;
    }

    public function setUseTime(?\DateTimeInterface $useTime): void
    {
        $this->useTime = $useTime;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): void
    {
        $this->contract = $contract;
    }
}
