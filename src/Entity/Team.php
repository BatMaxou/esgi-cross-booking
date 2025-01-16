<?php

namespace App\Entity;

use App\Entity\Reservation\TeamReservation;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'teams')]
    private Collection $members;

    #[ORM\ManyToOne(inversedBy: 'ownedTeams')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, TeamReservation>
     */
    #[ORM\OneToMany(targetEntity: TeamReservation::class, mappedBy: 'team')]
    private Collection $teamReservations;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->teamReservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(User $member): static
    {
        $this->members->removeElement($member);

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;

        return $this;
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
     * @return Collection<int, TeamReservation>
     */
    public function getTeamReservations(): Collection
    {
        return $this->teamReservations;
    }

    public function addTeamReservation(TeamReservation $teamReservation): static
    {
        if (!$this->teamReservations->contains($teamReservation)) {
            $this->teamReservations->add($teamReservation);
            $teamReservation->setTeam($this);
        }

        return $this;
    }

    public function removeTeamReservation(TeamReservation $teamReservation): static
    {
        if ($this->teamReservations->removeElement($teamReservation)) {
            // set the owning side to null (unless already changed)
            if ($teamReservation->getTeam() === $this) {
                $teamReservation->setTeam(null);
            }
        }

        return $this;
    }
}
