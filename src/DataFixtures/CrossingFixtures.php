<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Crossing;
use App\Entity\Raft;
use App\Entity\Route;
use App\Repository\RaftRepository;
use App\Repository\RouteRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CrossingFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    private ObjectManager $manager;
    /** @var Raft[] */
    private array $rafts;
    /** @var Route[] */
    private array $routes;

    public function __construct(
        private readonly RaftRepository $raftRepository,
        private readonly RouteRepository $routeRepository,
    ) {
        $this->initializeFaker();
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->routes = $this->routeRepository->findAll();
        $this->rafts = $this->raftRepository->findAll();

        $this->createCrossing(10);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RaftFixtures::class,
            RouteFixtures::class,
        ];
    }

    private function createCrossing(int $count): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $rafts = $this->faker->randomElements($this->rafts, $this->faker->numberBetween(1, 3));
            $route = $this->faker->randomElement($this->routes);
            if (!$route instanceof Route) {
                throw new \LogicException('Entity Route not found');
            }

            $crossing = (new Crossing())
                ->setDate(new \DateTimeImmutable($this->faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d H:i:s')))
                ->setRoute($route);

            foreach ($rafts as $raft) {
                if (!$raft instanceof Raft) {
                    throw new \LogicException('Entity Raft not found');
                }

                $crossing->addRaft($raft);
            }

            $this->manager->persist($crossing);
        }
    }
}
