<?php

namespace App\Entity;

use App\Repository\StructurePermissionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StructurePermissionsRepository::class)]
class StructurePermissions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $permissions = [];

    #[ORM\Column]
    private ?bool $is_active = null;

    #[ORM\OneToMany(mappedBy: 'structurePermissions', targetEntity: Structure::class)]
    private Collection $structure_id;

    public function __construct()
    {
        $this->structure_id = new ArrayCollection();
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
     * @return Collection<int, Structure>
     */
    public function getStructureId(): Collection
    {
        return $this->structure_id;
    }

    public function addStructureId(Structure $structureId): self
    {
        if (!$this->structure_id->contains($structureId)) {
            $this->structure_id->add($structureId);
            $structureId->setStructurePermissions($this);
        }

        return $this;
    }

    public function removeStructureId(Structure $structureId): self
    {
        if ($this->structure_id->removeElement($structureId)) {
            // set the owning side to null (unless already changed)
            if ($structureId->getStructurePermissions() === $this) {
                $structureId->setStructurePermissions(null);
            }
        }

        return $this;
    }
}
