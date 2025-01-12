<?php

namespace App\Entity;

use App\Repository\RaftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaftRepository::class)]
class Raft
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    /**
     * @var Collection<int, Crossing>
     */
    #[ORM\ManyToMany(targetEntity: Crossing::class, mappedBy: 'rafts')]
    private Collection $crossings;

    public function __construct()
    {
        $this->crossings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setImage(string $image): static
    {
        $this->image = $image;

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
}
