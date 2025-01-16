<?php

namespace App\Entity\Reservation;

use App\Entity\Reservation\Reservation;
use App\Entity\User;
use App\Repository\SimpleReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SimpleReservationRepository::class)]
class SimpleReservation extends Reservation
{
    #[ORM\ManyToOne(inversedBy: 'simpleReservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $passenger = null;

    public function getPassenger(): ?User
    {
        return $this->passenger;
    }

    public function setPassenger(?User $passenger): static
    {
        $this->passenger = $passenger;

        return $this;
    }
}
