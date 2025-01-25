<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Port;
use App\Entity\Route;
use App\Enum\CountryEnum;
use App\Repository\PortRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RouteFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    private ObjectManager $manager;

    /** @var Port[] */
    private array $ukPorts;
    /** @var Port[] */
    private array $frPorts;
    /** @var array<string, array<string, bool>> */
    private array $alreadyCalledMap = [];

    public function __construct(
        private readonly PortRepository $portRepository,
    ) {
        $this->initializeFaker();
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->ukPorts = $this->portRepository->findByCountry(CountryEnum::UNITED_KINGDOM);
        $this->frPorts = $this->portRepository->findByCountry(CountryEnum::FRANCE);

        $this->createRoutes(10);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PortFixtures::class,
        ];
    }

    private function createRoutes(int $count): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $from = $this->faker->randomElement($this->frPorts);
            $to = $this->faker->randomElement($this->ukPorts);

            if (!$from instanceof Port || !$to instanceof Port) {
                throw new \LogicException('Entity Port not found');
            }

            if (isset($this->alreadyCalledMap[$from->getId()][$to->getId()])) {
                continue;
            }

            $this->alreadyCalledMap[$from->getId()][$to->getId()] = true;

            $route = (new Route())
                ->setFromPort($from)
                ->setToPort($to);

            $this->manager->persist($route);
        }
    }
}
