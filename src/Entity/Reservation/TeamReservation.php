<?php

namespace App\Entity\Reservation;

use App\Entity\Reservation\Reservation;
use App\Entity\Team;
use App\Repository\TeamReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamReservationRepository::class)]
class TeamReservation extends Reservation
{
    #[ORM\ManyToOne(inversedBy: 'teamReservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team = null;

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }
}
