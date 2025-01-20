<?php

namespace App\Entity;

use App\Repository\RouteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RouteRepository::class)]
class Route
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'routes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Port $fromPort = null;

    #[ORM\ManyToOne(inversedBy: 'routes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Port $toPort = null;

    /**
     * @var Collection<int, Crossing>
     */
    #[ORM\OneToMany(targetEntity: Crossing::class, mappedBy: 'route', cascade: ['remove'])]
    private Collection $crossings;

    public function __construct()
    {
        $this->crossings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromPort(): ?Port
    {
        return $this->fromPort;
    }

    public function setFromPort(?Port $fromPort): static
    {
        $this->fromPort = $fromPort;

        return $this;
    }

    public function getToPort(): ?Port
    {
        return $this->toPort;
    }

    public function setToPort(?Port $toPort): static
    {
        $this->toPort = $toPort;

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
            $crossing->setRoute($this);
        }

        return $this;
    }

    public function removeCrossing(Crossing $crossing): static
    {
        if ($this->crossings->removeElement($crossing)) {
            // set the owning side to null (unless already changed)
            if ($crossing->getRoute() === $this) {
                $crossing->setRoute(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s -> %s', $this->fromPort, $this->toPort);
    }
}
