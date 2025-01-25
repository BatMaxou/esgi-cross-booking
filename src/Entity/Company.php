<?php

namespace App\Entity;

use App\Entity\Trait\UuidTrait;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    use UuidTrait {
        __construct as initializeUuid;
    }

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Raft>
     */
    #[ORM\OneToMany(targetEntity: Raft::class, mappedBy: 'company', cascade: ['remove'])]
    private Collection $rafts;

    public function __construct()
    {
        $this->initializeUuid();
        $this->rafts = new ArrayCollection();
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

    /**
     * @return Collection<int, Raft>
     */
    public function getRafts(): Collection
    {
        return $this->rafts;
    }

    public function addRaft(Raft $raft): static
    {
        if (!$this->rafts->contains($raft)) {
            $this->rafts->add($raft);
            $raft->setCompany($this);
        }

        return $this;
    }

    public function removeRaft(Raft $raft): static
    {
        if ($this->rafts->removeElement($raft)) {
            // set the owning side to null (unless already changed)
            if ($raft->getCompany() === $this) {
                $raft->setCompany(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
