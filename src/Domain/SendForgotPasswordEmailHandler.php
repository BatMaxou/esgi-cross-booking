<?php

namespace App\Domain;

use App\Service\Mailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendForgotPasswordEmailHandler
{
    public function __construct(private readonly Mailer $mailer)
    {
    }

    public function __invoke(SendForgotPasswordEmailCommand $command): void
    {
        $this->mailer->sendForgotPasswordEmail($command->user, $command->resetUrl, $command->token);
    }
}
