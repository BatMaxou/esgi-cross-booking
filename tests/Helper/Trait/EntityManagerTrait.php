<?php

namespace App\Tests\Helper\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

trait EntityManagerTrait
{
    private EntityManagerInterface $em;

    public function initEntityManager(): void
    {
        assert($this instanceof KernelTestCase, 'This trait can only be used in a KernelTestCase');

        $this->em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
}
