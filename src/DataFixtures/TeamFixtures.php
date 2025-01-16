<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Team;
use App\Entity\User;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TeamFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    private ObjectManager $manager;
    private array $users;

    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
        $this->initializeFaker();
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->users = $this->userRepository->findAll();

        $this->createTeams(4);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    private function createTeams(int $count): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $team = (new Team())
                ->setName($this->faker->company)
                ->setCreator($this->faker->randomElement($this->users));

            for ($j = 0; $j < $this->faker->numberBetween(1, 8); ++$j) {
                $member = $this->faker->randomElement($this->users);
                if ($member->getId() === $team->getCreator()->getId()) {
                    continue;
                }

                $team->addMember($member);
            }

            $this->manager->persist($team);
        }
    }
}
