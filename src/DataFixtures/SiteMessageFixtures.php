<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\SiteMessage;
use App\Enum\SiteMessagePlaceEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SiteMessageFixtures extends Fixture
{
    use FakerFixtureTrait;

    private ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->createMessages();

        $manager->flush();
    }

    private function createMessages(): void
    {
        foreach (SiteMessagePlaceEnum::cases() as $place) {
            $message = (new SiteMessage())
                ->setPlace($place)
                ->setContent($this->faker->realText(200));

            $this->manager->persist($message);
        }
    }
}
