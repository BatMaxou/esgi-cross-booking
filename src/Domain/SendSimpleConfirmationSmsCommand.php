<?php

namespace App\Domain;

use App\Entity\Crossing;
use App\Entity\User;

class SendSimpleConfirmationSmsCommand
{
    public function __construct(
        public User $user,
        public Crossing $crossing,
    ) {
    }
}
