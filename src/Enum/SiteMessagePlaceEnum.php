<?php

namespace App\Enum;

enum SiteMessagePlaceEnum: string
{
    case HOME = 'home';
    case BAN = 'ban';
    case PASSED_CROSSING = 'passed_crossing';
    case UNLIMITED_CROSSING = 'unlimited_crossing';
}
