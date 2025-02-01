<?php

namespace App\Tests\Helper\Trait;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

trait BootTrait
{
    public function boot(): void
    {
        assert($this instanceof KernelTestCase, 'This trait can only be used in a KernelTestCase');

        if (!static::$booted) {
            static::bootKernel();
        }
    }
}
