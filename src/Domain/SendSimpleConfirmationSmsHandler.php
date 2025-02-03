<?php

namespace App\Domain;

use App\Service\SmsNotifier;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendSimpleConfirmationSmsHandler
{
    private const CONTENT = 'Bonjour %s, l\'équipe de CrossBooking confirme votre réservation de la traversée %s >> %s du %s à %s. Merci de votre confiance.';

    public function __construct(private readonly SmsNotifier $smsNotifier)
    {
    }

    public function __invoke(SendSimpleConfirmationSmsCommand $command): void
    {
        $phone = $command->user->getPhone();
        $firstName = $command->user->getFirstName();
        $fromPort = $command->crossing->getRoute()?->getFromPort()?->getName();
        $toPort = $command->crossing->getRoute()?->getToPort()?->getName();
        $date = $command->crossing->getDate()?->format('d/m/Y') ?? null;
        $hour = $command->crossing->getDate()?->format('H:i') ?? null;

        if (!$phone || !$firstName || !$fromPort || !$toPort || !$date || !$hour) {
            return;
        }

        $this->smsNotifier->send($phone, sprintf(
            self::CONTENT,
            $firstName,
            $fromPort,
            $toPort,
            $date,
            $hour
        ));
    }
}
