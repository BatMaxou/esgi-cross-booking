<?php

namespace App\Enum;

enum VoterRoleEnum: string
{
    case ADMIN = 'VOTER_ROLE_ADMIN';
    case BANNED = 'VOTER_ROLE_BANNED';
    case UNBANED = 'VOTER_ROLE_UNBANED';
}
