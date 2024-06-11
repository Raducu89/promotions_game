<?php

namespace App\Entity;

use App\Repository\PrizeDistributionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrizeDistributionRepository::class)]
class PrizeDistribution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Prizes::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prizes $prize = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?bool $distributed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPrizeId(): ?int
    {
        return $this->prize_id;
    }

    public function setPrizeId(int $prze_id): static
    {
        $this->prize_id = $prze_id;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function isDistributed(): ?bool
    {
        return $this->distributed;
    }

    public function setDistributed(bool $distributed): static
    {
        $this->distributed = $distributed;

        return $this;
    }
}
