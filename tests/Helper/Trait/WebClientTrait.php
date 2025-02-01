<?php

namespace App\Tests\Helper\Trait;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

trait WebClientTrait
{
    private KernelBrowser $client;

    public function initClient(): void
    {
        assert($this instanceof WebTestCase, 'This trait can only be used in a WebTestCase');

        $this->client = static::createClient();
    }
}
