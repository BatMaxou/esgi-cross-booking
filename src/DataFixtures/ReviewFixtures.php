<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Crossing;
use App\Entity\Review;
use App\Entity\User;
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
    /** @var User[] */
    private array $users;
    /** @var Crossing[] */
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
                $crossing = $this->faker->randomElement($this->crossings);
                if (!$crossing instanceof Crossing) {
                    throw new \LogicException('Entity Crossing not found');
                }

                $review = (new Review())
                    ->setAuthor($user)
                    ->setDate(new \DateTimeImmutable($this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i:s')))
                    ->setContent($this->faker->realText(200))
                    ->setCrossing($crossing);

                $this->manager->persist($review);
            }
        }
    }
}
