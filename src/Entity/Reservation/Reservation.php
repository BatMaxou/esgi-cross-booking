<?php

namespace App\Entity\Reservation;

use App\Entity\Crossing;
use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap(['simple' => SimpleReservation::class, 'team' => TeamReservation::class])]
abstract class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Crossing $crossing = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCrossing(): ?Crossing
    {
        return $this->crossing;
    }

    public function setCrossing(?Crossing $crossing): static
    {
        $this->crossing = $crossing;

        return $this;
    }
}
