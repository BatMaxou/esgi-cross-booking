<?php

namespace App\DataFixtures;

use App\Entity\SiteMessage;
use App\Enum\SiteMessagePlaceEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SiteMessageFixtures extends Fixture
{
    private ObjectManager $manager;

    /**
     * @var string[]
     */
    public const MESSAGES = [
        SiteMessagePlaceEnum::HOME->value => 'Le paiement de votre traversée en radeau s’effectue directement sur place au moment de l’embarquement. Assurez-vous d’avoir le montant exact en espèces ou un moyen de paiement accepté avant votre départ. Toutes les informations relatives à votre réservation, y compris les détails du paiement et de l’embarquement, vous seront transmises par SMS. Merci de votre confiance et à bientôt sur l’eau ! 🌊🚣‍♂️',
        SiteMessagePlaceEnum::BAN->value => 'Votre compte a été suspendu et vous ne pouvez plus accéder aux fonctionnalités réservées aux utilisateurs inscrits. Toutefois, vous pouvez toujours naviguer sur l’espace public du site et consulter les informations disponibles.',
        SiteMessagePlaceEnum::PASSED_CROSSING->value => 'Cette traversée à déjà eu lieu, vous ne pouvez donc plus y participer.',
        SiteMessagePlaceEnum::UNLIMITED_CROSSING->value => 'Cette traversée comptabilise déjà le nombre recommandé de participants. Vous pouvez toutefois vous inscrire, nous trouverons toujours une solution pour vous accueillir à bord !',
    ];

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
                ->setContent(self::MESSAGES[$place->value]);

            $this->manager->persist($message);
        }
    }
}
