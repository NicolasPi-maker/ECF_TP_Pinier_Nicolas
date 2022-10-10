<?php

namespace App\Entity;

use App\Repository\FranchiseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FranchiseRepository::class)]
class Franchise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private ?string $client_name = null;

    #[ORM\Column(length: 255)]
    private ?string $client_address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo_url = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $technical_contact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commercial_contact = null;

    #[ORM\Column(length: 120)]
    private ?string $short_description = null;

    #[ORM\Column(length: 255)]
    private ?string $full_description = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_active = null;

    #[ORM\OneToMany(mappedBy: 'franchise_id', targetEntity: Structure::class, cascade: ['remove'])]
    private Collection $structures;

    #[ORM\Column(nullable: true)]
    private ?bool $sell_drink = null;

    #[ORM\Column(nullable: true)]
    private ?bool $manage_schedule = null;

    #[ORM\Column(nullable: true)]
    private ?bool $create_newsletter = null;

    #[ORM\Column(nullable: true)]
    private ?bool $add_promotion = null;

    #[ORM\Column(nullable: true)]
    private ?bool $sell_food = null;

    #[ORM\Column(nullable: true)]
    private ?bool $create_event = null;

    #[ORM\ManyToOne(inversedBy: 'franchises')]
    private ?User $user_id = null;

    public function __construct()
    {
        $this->structures = new ArrayCollection();
    }

    public function __toString()
    {
      return $this->client_name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientName(): ?string
    {
        return $this->client_name;
    }

    public function setClientName(string $client_name): self
    {
        $this->client_name = $client_name;

        return $this;
    }

    public function getClientAddress(): ?string
    {
        return $this->client_address;
    }

    public function setClientAddress(string $client_address): self
    {
        $this->client_address = $client_address;

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

    public function getLogoUrl(): ?string
    {
        return $this->logo_url;
    }

    public function setLogoUrl(?string $logo_url): self
    {
        $this->logo_url = $logo_url;

        return $this;
    }

    public function getTechnicalContact(): ?string
    {
        return $this->technical_contact;
    }

    public function setTechnicalContact(?string $technical_contact): self
    {
        $this->technical_contact = $technical_contact;

        return $this;
    }

    public function getCommercialContact(): ?string
    {
        return $this->commercial_contact;
    }

    public function setCommercialContact(?string $commercial_contact): self
    {
        $this->commercial_contact = $commercial_contact;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->short_description;
    }

    public function setShortDescription(string $short_description): self
    {
        $this->short_description = $short_description;

        return $this;
    }

    public function getFullDescription(): ?string
    {
        return $this->full_description;
    }

    public function setFullDescription(string $full_description): self
    {
        $this->full_description = $full_description;

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

    /**
     * @return Collection<int, Structure>
     */
    public function getStructures(): Collection
    {
        return $this->structures;
    }

    public function addStructure(Structure $structure): self
    {
        if (!$this->structures->contains($structure)) {
            $this->structures->add($structure);
            $structure->setFranchiseId($this);
        }

        return $this;
    }

    public function removeStructure(Structure $structure): self
    {
        if ($this->structures->removeElement($structure)) {
            // set the owning side to null (unless already changed)
            if ($structure->getFranchiseId() === $this) {
                $structure->setFranchiseId(null);
            }
        }

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

    public function isCreateEvent(): ?bool
    {
        return $this->create_event;
    }

    public function setCreateEvent(?bool $create_event): self
    {
        $this->create_event = $create_event;

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
}
