<?php

namespace App\Domain;

use App\Entity\User;

class SendForgotPasswordEmailCommand
{
    public function __construct(
        public User $user,
        public string $token,
    ) {
    }
}
