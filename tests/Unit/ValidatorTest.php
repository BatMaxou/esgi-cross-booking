<?php

namespace App\Tests\Unit;

use App\Service\Validator;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\BootTrait;
use App\Tests\Helper\Trait\EntityManagerTrait;
use App\Tests\Helper\Trait\ServiceProviderTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ValidatorTest extends KernelTestCase
{
    use BootTrait;
    use EntityManagerTrait;
    use ServiceProviderTrait;

    private Validator $validator;

    protected function setUp(): void
    {
        $this->boot();
        $this->initEntityManager();
        $this->validator = $this->getService(Validator::class);
    }

    public function test_validator_return_true_to_matching_password(): void
    {
        $this->assertTrue($this->validator->validatePassword('pswd', 'pswd'));
    }

    public function test_validator_return_false_to_not_matching_password(): void
    {
        $this->assertFalse($this->validator->validatePassword('pswd', 'pswd-2'));
    }

    public function test_validator_return_false_to_empty_password(): void
    {
        $this->assertFalse($this->validator->validatePassword('', ''));
    }

    public function test_validator_return_true_to_valid_and_unique_email(): void
    {
        $this->assertTrue($this->validator->validateEmail('test@test.com'));
    }

    public function test_validator_return_false_to_invalid_email(): void
    {
        $this->assertFalse($this->validator->validateEmail('test.com'));
    }

    public function test_validator_return_false_to_not_unique_email(): void
    {
        $user = ThereIs::anUser()->withEmail('test_validator_return_false_to_not_unique_email@test.com')->build();
        $this->em->persist($user);
        $this->em->flush();

        $this->assertFalse($this->validator->validateEmail('test_validator_return_false_to_not_unique_email@test.com'));
    }
}
