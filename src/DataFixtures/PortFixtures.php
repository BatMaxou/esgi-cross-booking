<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Port;
use App\Enum\CountryEnum;

class PortFixtures extends Fixture
{
    use FakerFixtureTrait;

    private const EN_PORTS = [
        'Dover',
        'Folkestone',
        'Newhaven',
        'Portsmouth',
        'Poole',
        'Newhaven',
        'Douvres',
        'NewPort',
    ];

    private const FR_PORTS = [
        'Calais',
        'Boulogne-sur-Mer',
        'Dieppe',
        'Le Havre',
        'Cherbourg',
        'Saint-Malo',
    ];

    private ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->createPorts();

        $manager->flush();
    }

    private function createPorts(): void
    {
        foreach (self::EN_PORTS as $port) {
            $port = (new Port())
                ->setName($port)
                ->setCountry(CountryEnum::UNITED_KINGDOM);

            $this->manager->persist($port);
        }

        foreach (self::FR_PORTS as $port) {
            $port = (new Port())
                ->setName($port)
                ->setCountry(CountryEnum::FRANCE);

            $this->manager->persist($port);
        }
    }
}
