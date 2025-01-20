<?php

namespace App\Entity;

use App\Entity\Reservation\SimpleReservation;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, enumType: RoleEnum::class)]
    private array $roles = [];

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'author', cascade: ['remove'])]
    private Collection $reviews;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\ManyToMany(targetEntity: Team::class, mappedBy: 'members', cascade: ['remove'])]
    private Collection $teams;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\OneToMany(targetEntity: Team::class, mappedBy: 'creator', cascade: ['remove'])]
    private Collection $ownedTeams;

    /**
     * @var Collection<int, SimpleReservation>
     */
    #[ORM\OneToMany(targetEntity: SimpleReservation::class, mappedBy: 'passenger', cascade: ['remove'])]
    private Collection $simpleReservations;

    public function __construct()
    {

        $this->roles[] = RoleEnum::USER;

        $this->reviews = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->ownedTeams = new ArrayCollection();
        $this->simpleReservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password, $alreadyHashed = false): static
    {
        if ($alreadyHashed) {
            $this->password = $password;
        } else {
            $this->plainPassword = $password;
            $this->password = null;
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setAuthor($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getAuthor() === $this) {
                $review->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return RoleEnum[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(RoleEnum $role): static
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function hasRole(RoleEnum $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    public function removeRole(RoleEnum $role): static
    {
        $key = array_search($role, $this->roles, true);

        if ($key !== false) {
            unset($this->roles[$key]);
        }

        return $this;
    }

    public function isBanned(): bool
    {
        return $this->hasRole(RoleEnum::BANNED);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleEnum::ADMIN);
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): static
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
            $team->addMember($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): static
    {
        if ($this->teams->removeElement($team)) {
            $team->removeMember($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getOwnedTeams(): Collection
    {
        return $this->ownedTeams;
    }

    public function addOwnedTeam(Team $ownedTeam): static
    {
        if (!$this->ownedTeams->contains($ownedTeam)) {
            $this->ownedTeams->add($ownedTeam);
            $ownedTeam->setCreator($this);
        }

        return $this;
    }

    public function removeOwnedTeam(Team $ownedTeam): static
    {
        if ($this->ownedTeams->removeElement($ownedTeam)) {
            // set the owning side to null (unless already changed)
            if ($ownedTeam->getCreator() === $this) {
                $ownedTeam->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SimpleReservation>
     */
    public function getSimpleReservations(): Collection
    {
        return $this->simpleReservations;
    }

    public function addSimpleReservation(SimpleReservation $simpleReservation): static
    {
        if (!$this->simpleReservations->contains($simpleReservation)) {
            $this->simpleReservations->add($simpleReservation);
            $simpleReservation->setPassenger($this);
        }

        return $this;
    }

    public function removeSimpleReservation(SimpleReservation $simpleReservation): static
    {
        if ($this->simpleReservations->removeElement($simpleReservation)) {
            // set the owning side to null (unless already changed)
            if ($simpleReservation->getPassenger() === $this) {
                $simpleReservation->setPassenger(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }
}
