<?php

namespace App\Entity;

use App\Repository\GlobalPermissionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GlobalPermissionsRepository::class)]
class GlobalPermissions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $permissions = [];

    #[ORM\Column]
    private ?bool $is_active = null;

    #[ORM\OneToMany(mappedBy: 'globalPermissions', targetEntity: Franchise::class)]
    private Collection $franchise_id;

    public function __construct()
    {
        $this->franchise_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): self
    {
        $this->permissions = $permissions;

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
     * @return Collection<int, Franchise>
     */
    public function getFranchiseId(): Collection
    {
        return $this->franchise_id;
    }

    public function addFranchiseId(Franchise $franchiseId): self
    {
        if (!$this->franchise_id->contains($franchiseId)) {
            $this->franchise_id->add($franchiseId);
            $franchiseId->setGlobalPermissions($this);
        }

        return $this;
    }

    public function removeFranchiseId(Franchise $franchiseId): self
    {
        if ($this->franchise_id->removeElement($franchiseId)) {
            // set the owning side to null (unless already changed)
            if ($franchiseId->getGlobalPermissions() === $this) {
                $franchiseId->setGlobalPermissions(null);
            }
        }

        return $this;
    }
}
