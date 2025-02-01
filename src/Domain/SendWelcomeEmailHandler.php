<?php

namespace App\Domain;

use App\Service\Mailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendWelcomeEmailHandler
{
    public function __construct(private readonly Mailer $mailer)
    {
    }

    public function __invoke(SendWelcomeEmailCommand $command): void
    {
        $this->mailer->sendWelcomeEmail($command->user, $command->loginUrl);
    }
}
