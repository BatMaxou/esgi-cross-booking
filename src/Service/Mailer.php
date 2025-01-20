<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\EmailTypeEnum;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {}

    public function sendForgotPasswordEmail(User $user, string $token): void
    {
        $this->send(
            $user,
            'Réinitialisation de mot de passe',
            EmailTypeEnum::FORGOT_PASSWORD,
            ['token' => $token]
        );
    }

    public function sendWelcomeEmail(User $user): void
    {
        $this->send(
            $user,
            'Bienvenue sur CrossBooking',
            EmailTypeEnum::WELCOME
        );
    }

    private function send(User $user, string $subject, EmailTypeEnum $type, array $context = []): void
    {
        $template = (new TemplatedEmail())
            ->to($user->getEmail())
            ->from('team@crossbooking.fr')
            ->subject($subject)
            ->htmlTemplate(sprintf('email/%s.html.twig', $type->value))
            ->context([
                'firstName' => $user->getFirstName(),
                ...$context,
            ]);

        $this->mailer->send($template);
    }
}
