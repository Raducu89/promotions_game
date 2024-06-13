<?php

namespace App\Entity;

use App\Repository\UserPrizesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPrizesRepository::class)]
class UserPrizes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    #[ORM\ManyToOne(targetEntity: PrizeDistribution::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?PrizeDistribution $prizeDistribution = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_played = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getPrizeDistribution(): ?PrizeDistribution
    {
        return $this->prizeDistribution;
    }

    public function setPrizeDistribution(?PrizeDistribution $prizeDistribution): static
    {
        $this->prizeDistribution = $prizeDistribution;
        return $this;
    }

    public function getDatePlayed(): ?\DateTimeInterface
    {
        return $this->date_played;
    }

    public function setDatePlayed(\DateTimeInterface $date_played): static
    {
        $this->date_played = $date_played;
        return $this;
    }
}
