<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Company;
use App\Entity\Raft;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RaftFixtures extends Fixture
{
    use FakerFixtureTrait;

    private const IMAGE_PATH = 'images/fixtures/';

    private const COMPANY = [
        'low' => 'Bloco',
        'medium' => 'Catamarana',
        'premium' => 'Costa Esmeraldar',
    ];

    private const RAFTS = [
        'low' => [
            'name' => ['Radodo', 'Lebatodeminecraft', '5 Planches'],
            'places' => 10,
        ],
        'medium' => [
            'name' => ['100 âmes', 'Titanoc'],
            'places' => 100,
        ],
        'premium' => [
            'name' => ['El Pakbo', 'La chance sur 200', 'Glacier droit devant'],
            'places' => 200,
        ],
    ];

    private ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->createRafts();

        $manager->flush();
    }

    private function createRafts(): void
    {
        foreach (self::COMPANY as $key => $company) {
            $company = (new Company())->setName($company);
            $this->manager->persist($company);

            if (isset(self::RAFTS[$key]) && isset(self::RAFTS[$key]['name'])) {
                foreach (self::RAFTS[$key]['name'] as $name) {
                    $this->createRaft($key, $name, self::RAFTS[$key]['places'], $company);
                }
            }
        }
    }

    private function createRaft(string $key, string $name, int $places, Company $company): void
    {
        $raft = (new Raft())
            ->setName($name)
            ->setCompany($company)
            ->setPlaces($places)
            ->setImage(sprintf('%s%s.png', self::IMAGE_PATH, $key));

        $this->manager->persist($raft);
    }
}
