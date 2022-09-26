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

    #[ORM\Column(length: 255)]
    private ?string $logo_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    #[ORM\ManyToOne(inversedBy: 'structures')]
    private ?Franchise $franchise_id = null;

    #[ORM\ManyToOne(inversedBy: 'structures')]
    private ?User $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'structure_id')]
    private ?StructurePermissions $structurePermissions = null;

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

    public function getStructurePermissions(): ?StructurePermissions
    {
        return $this->structurePermissions;
    }

    public function setStructurePermissions(?StructurePermissions $structurePermissions): self
    {
        $this->structurePermissions = $structurePermissions;

        return $this;
    }
}
