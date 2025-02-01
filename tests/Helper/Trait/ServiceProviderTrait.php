<?php

namespace App\Tests\Helper\Trait;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

trait ServiceProviderTrait
{
    /**
     * @param class-string $class
     */
    public function getService(string $class): object
    {
        assert($this instanceof KernelTestCase, 'This trait can only be used in a KernelTestCase');

        $container = static::$kernel->getContainer();

        return $container->get($class);
    }
}
