<?php

namespace App\Entity;

use App\Repository\CrossingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CrossingRepository::class)]
class Crossing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'crossing')]
    private Collection $reservations;

    /**
     * @var Collection<int, Raft>
     */
    #[ORM\ManyToMany(targetEntity: Raft::class, inversedBy: 'crossings')]
    private Collection $rafts;

    #[ORM\ManyToOne(inversedBy: 'crossings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Route $route = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->rafts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setCrossing($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getCrossing() === $this) {
                $reservation->setCrossing(null);
            }
        }

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
        }

        return $this;
    }

    public function removeRaft(Raft $raft): static
    {
        $this->rafts->removeElement($raft);

        return $this;
    }

    public function getRoute(): ?Route
    {
        return $this->route;
    }

    public function setRoute(?Route $route): static
    {
        $this->route = $route;

        return $this;
    }
}