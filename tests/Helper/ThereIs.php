<?php

namespace App\Tests\Helper;

use App\Tests\Helper\Builder\UserBuilder;

class ThereIs
{
    public static function anUser(): UserBuilder
    {
        return new UserBuilder();
    }
}
