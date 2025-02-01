<?php

namespace App\Tests\Helper;

use App\Tests\Helper\Builder\AccessBuilder;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class When
{
    private static KernelBrowser $client;

    public static function setClient(KernelBrowser $client): void
    {
        self::$client = $client;
    }

    public static function access(): AccessBuilder
    {
        return new AccessBuilder(self::$client);
    }
}
