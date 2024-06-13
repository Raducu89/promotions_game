<?php

namespace App\Entity;

use App\Repository\PrizesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrizesRepository::class)]
class Prizes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $prize_id = null;

    #[ORM\Column(length: 100)]
    private ?string $prize_code = null;

    #[ORM\ManyToOne(targetEntity: Partners::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Partners $partner = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    private ?string $language = null;

    #[ORM\Column]
    private ?bool $is_available = null;

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

    public function setPrizeId(int $prize_id): static
    {
        $this->prize_id = $prize_id;
        return $this;
    }

    public function getPrizeCode(): ?string
    {
        return $this->prize_code;
    }

    public function setPrizeCode(string $prize_code): static
    {
        $this->prize_code = $prize_code;
        return $this;
    }

    public function getPartner(): ?Partners
    {
        return $this->partner;
    }

    public function setPartner(?Partners $partner): static
    {
        $this->partner = $partner;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;
        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->is_available;
    }

    public function setAvailable(bool $is_available): static
    {
        $this->is_available = $is_available;
        return $this;
    }
}
