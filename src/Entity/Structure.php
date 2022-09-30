<?php

namespace App\Entity;

use App\Repository\StructureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StructureRepository::class)]
class Structure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $structure_name = null;

    #[ORM\Column(length: 255)]
    private ?string $structure_address = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $manager_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    #[ORM\ManyToOne(inversedBy: 'structures')]
    private ?Franchise $franchise_id = null;

    #[ORM\ManyToOne(inversedBy: 'structures')]
    private ?User $user_id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $sell_drink = null;

    #[ORM\Column(nullable: true)]
    private ?bool $manage_schedule = null;

    #[ORM\Column(nullable: true)]
    private ?bool $create_newsletter = null;

    #[ORM\Column(nullable: true)]
    private ?bool $create_event = null;

    #[ORM\Column(nullable: true)]
    private ?bool $add_promotion = null;

    #[ORM\Column(nullable: true)]
    private ?bool $sell_food = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStructureName(): ?string
    {
        return $this->structure_name;
    }

    public function setStructureName(string $structure_name): self
    {
        $this->structure_name = $structure_name;

        return $this;
    }

    public function getStructureAddress(): ?string
    {
        return $this->structure_address;
    }

    public function setStructureAddress(string $structure_address): self
    {
        $this->structure_address = $structure_address;

        return $this;
    }

    public function getManagerName(): ?string
    {
        return $this->manager_name;
    }

    public function setManagerName(?string $manager_name): self
    {
        $this->manager_name = $manager_name;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logo_url;
    }

    public function setLogoUrl(string $logo_url): self
    {
        $this->logo_url = $logo_url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getFranchiseId(): ?Franchise
    {
        return $this->franchise_id;
    }

    public function setFranchiseId(?Franchise $franchise_id): self
    {
        $this->franchise_id = $franchise_id;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function isSellDrink(): ?bool
    {
        return $this->sell_drink;
    }

    public function setSellDrink(?bool $sell_drink): self
    {
        $this->sell_drink = $sell_drink;

        return $this;
    }

    public function isManageSchedule(): ?bool
    {
        return $this->manage_schedule;
    }

    public function setManageSchedule(?bool $manage_schedule): self
    {
        $this->manage_schedule = $manage_schedule;

        return $this;
    }

    public function isCreateNewsletter(): ?bool
    {
        return $this->create_newsletter;
    }

    public function setCreateNewsletter(?bool $create_newsletter): self
    {
        $this->create_newsletter = $create_newsletter;

        return $this;
    }

    public function isCreateEvent(): ?bool
    {
        return $this->create_event;
    }

    public function setCreateEvent(?bool $create_event): self
    {
        $this->create_event = $create_event;

        return $this;
    }

    public function isAddPromotion(): ?bool
    {
        return $this->add_promotion;
    }

    public function setAddPromotion(?bool $add_promotion): self
    {
        $this->add_promotion = $add_promotion;

        return $this;
    }

    public function isSellFood(): ?bool
    {
        return $this->sell_food;
    }

    public function setSellFood(?bool $sell_food): self
    {
        $this->sell_food = $sell_food;

        return $this;
    }
}
