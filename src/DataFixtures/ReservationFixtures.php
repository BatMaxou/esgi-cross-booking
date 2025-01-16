<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Reservation\SimpleReservation;
use App\Entity\Reservation\TeamReservation;
use App\Entity\Team;
use App\Entity\User;
use App\Repository\CrossingRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    private ObjectManager $manager;
    private array $users;
    private array $teams;
    private array $crossings;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TeamRepository $teamRepository,
        private readonly CrossingRepository $crossingRepository,
    ) {
        $this->initializeFaker();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CrossingFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->users = $this->userRepository->findAll();
        $this->teams = $this->teamRepository->findAll();
        $this->crossings = $this->crossingRepository->findAll();

        $this->createReservations();

        $manager->flush();
    }

    private function createReservations(): void
    {
        foreach ($this->users as $user) {
            $this->createSimpleReservations($user);
        }

        foreach ($this->teams as $team) {
            $this->createTeamReservations($team);
        }
    }

    private function createSimpleReservations(User $user): void
    {
        $alreadyPicked = [];

        for ($i = 0; $i < $this->faker->numberBetween(0, 2); ++$i) {
            $crossing = $this->faker->randomElement($this->crossings);

            if (in_array($crossing->getId(), $alreadyPicked)) {
                continue;
            }

            $alreadyPicked[] = $crossing->getId();

            $reservation = (new SimpleReservation())
                ->setCrossing($crossing)
                ->setPassenger($user);

            $this->manager->persist($reservation);
        }
    }

    private function createTeamReservations(Team $team): void
    {
        $alreadyPicked = [];

        for ($i = 0; $i < $this->faker->numberBetween(0, 1); ++$i) {
            $crossing = $this->faker->randomElement($this->crossings);

            if (in_array($crossing->getId(), $alreadyPicked)) {
                continue;
            }

            $alreadyPicked[] = $crossing->getId();

            $reservation = (new TeamReservation())
                ->setCrossing($crossing)
                ->setTeam($team);

            $this->manager->persist($reservation);
        }
    }
}
