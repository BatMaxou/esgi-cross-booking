<?php

namespace App\Enum;

enum EmailTypeEnum: string
{
    case FORGOT_PASSWORD = 'forgot';
    case WELCOME = 'welcome';
}
