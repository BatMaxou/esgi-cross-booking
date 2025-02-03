<?php

namespace App\Domain;

use App\Entity\Crossing;
use App\Entity\Team;

class SendTeamConfirmationSmsCommand
{
    public function __construct(
        public Team $team,
        public Crossing $crossing,
    ) {
    }
}
