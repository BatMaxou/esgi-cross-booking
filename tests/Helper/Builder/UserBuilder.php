<?php

namespace App\Tests\Helper\Builder;

use App\Entity\User;
use App\Enum\RoleEnum;
use Faker\Factory;

class UserBuilder
{
    private User $user;

    public function __construct()
    {
        $faker = Factory::create();

        $this->user = (new User())
          ->setFirstName($faker->firstName())
          ->setLastName($faker->lastName())
          ->setEmail($faker->email())
          ->setPhone($faker->phoneNumber())
          ->setPassword('azerty');
    }

    public function build(): User
    {
        return $this->user;
    }

    public function withEmail(string $email): self
    {
        $this->user->setEmail($email);

        return $this;
    }

    public function withAdditionnalRole(RoleEnum $role): self
    {
        $this->user->addRole($role);

        return $this;
    }
}
