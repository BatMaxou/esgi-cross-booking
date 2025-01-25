<?php

namespace App\Entity;

use App\Entity\Reservation\TeamReservation;
use App\Entity\Trait\UuidTrait;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    use UuidTrait {
        __construct as initializeUuid;
    }

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'teams', cascade: ['remove'])]
    private Collection $members;

    #[ORM\ManyToOne(inversedBy: 'ownedTeams')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, TeamReservation>
     */
    #[ORM\OneToMany(targetEntity: TeamReservation::class, mappedBy: 'team', cascade: ['remove'])]
    private Collection $teamReservations;

    public function __construct()
    {
        $this->initializeUuid();
        $this->members = new ArrayCollection();
        $this->teamReservations = new ArrayCollection();
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

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
