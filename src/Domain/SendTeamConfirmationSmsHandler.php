<?php

namespace App\Domain;

use App\Service\SmsNotifier;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendTeamConfirmationSmsHandler
{
    private const CONTENT = 'Bonjour %s, l\'équipe de CrossBooking confirme la réservation de l\'équipe %s pour la traversée %s >> %s du %s à %s. Merci de votre confiance.';

    public function __construct(private readonly SmsNotifier $smsNotifier)
    {
    }

    public function __invoke(SendTeamConfirmationSmsCommand $command): void
    {
        $teamName = $command->team->getName();
        $fromPort = $command->crossing->getRoute()?->getFromPort()?->getName();
        $toPort = $command->crossing->getRoute()?->getToPort()?->getName();
        $date = $command->crossing->getDate()?->format('d/m/Y') ?? null;
        $hour = $command->crossing->getDate()?->format('H:i') ?? null;

        if (!$teamName || !$fromPort || !$toPort || !$date || !$hour) {
            return;
        }

        foreach ([$command->team->getCreator(), ...$command->team->getMembers()] as $recipient) {
            if (!$recipient) {
                continue;
            }

            $phone = $recipient->getPhone() ?? null;
            $firstName = $recipient->getFirstName() ?? null;

            if (!$phone || !$firstName) {
                continue;
            }

            $this->smsNotifier->send($phone, sprintf(
                self::CONTENT,
                $firstName,
                $teamName,
                $fromPort,
                $toPort,
                $date,
                $hour
            ));
        }
    }
}
