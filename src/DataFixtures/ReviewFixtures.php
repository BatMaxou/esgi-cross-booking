<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Review;
use App\Repository\CrossingRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    private ObjectManager $manager;
    private array $users;
    private array $crossings;

    public function __construct(
        private readonly UserRepository $userRepository,
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
        $this->crossings = $this->crossingRepository->findAll();

        $this->createReviews();

        $manager->flush();
    }

    private function createReviews(): void
    {
        foreach ($this->users as $user) {
            for ($i = 0; $i < $this->faker->numberBetween(0, 2); ++$i) {
                $review = (new Review())
                    ->setAuthor($user)
                    ->setContent($this->faker->realText(200))
                    ->setCrossing($this->faker->randomElement($this->crossings));

                $this->manager->persist($review);
            }
        }
    }
}
