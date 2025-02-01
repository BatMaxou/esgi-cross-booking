<?php

namespace App\Domain;

use App\Entity\User;

class SendWelcomeEmailCommand
{
    public function __construct(public User $user)
    {
    }
}
