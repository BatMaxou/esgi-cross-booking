<?php

namespace App\Entity;

use App\Entity\Trait\UuidTrait;
use App\Repository\RaftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaftRepository::class)]
class Raft
{
    use UuidTrait {
        __construct as initializeUuid;
    }

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * @var Collection<int, Crossing>
     */
    #[ORM\ManyToMany(targetEntity: Crossing::class, mappedBy: 'rafts', cascade: ['remove'])]
    private Collection $crossings;

    #[ORM\Column]
    private ?int $places = null;

    #[ORM\ManyToOne(inversedBy: 'rafts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    public function __construct()
    {
        $this->initializeUuid();
        $this->crossings = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        if (null !== $image) {
            $this->image = $image;
        }

        return $this;
    }

    /**
     * @return Collection<int, Crossing>
     */
    public function getCrossings(): Collection
    {
        return $this->crossings;
    }

    public function addCrossing(Crossing $crossing): static
    {
        if (!$this->crossings->contains($crossing)) {
            $this->crossings->add($crossing);
            $crossing->addRaft($this);
        }

        return $this;
    }

    public function removeCrossing(Crossing $crossing): static
    {
        if ($this->crossings->removeElement($crossing)) {
            $crossing->removeRaft($this);
        }

        return $this;
    }

    public function getPlaces(): ?int
    {
        return $this->places;
    }

    public function setPlaces(int $places): static
    {
        $this->places = $places;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }
}
