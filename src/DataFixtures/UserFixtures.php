<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\User;
use App\Enum\RoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    use FakerFixtureTrait;

    private ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->createAdmins(3);
        $this->createUsers(20);
        $this->createBannedUsers(10);

        $manager->flush();
    }

    private function createUsers(int $count, ?callable $process = null): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $user = $process ? $process() : $this->createUser();

            if (!$user instanceof User) {
                throw new \LogicException('Entity User not found');
            }

            $this->manager->persist($user);
        }
    }

    private function createAdmins(int $count): void
    {
        $adminType = $this->createAdmin('admin@gmail.com', 'Admin');
        $this->createUsers($count, $this->createAdmin(...));

        $this->manager->persist($adminType);
    }

    private function createBannedUsers(int $count): void
    {
        $this->createUsers($count, $this->createBannedUser(...));
    }

    private function createUser(?string $email = null, ?string $firstName = null, ?string $lastName = null): User
    {
        $user = (new User())
            ->setEmail($email ?? $this->faker->email())
            ->setFirstName($firstName ?? $this->faker->firstName())
            ->setLastName($lastName ?? $this->faker->lastName());

        return $user->setPassword('azerty');
    }

    private function createAdmin(?string $email = null, ?string $username = null): User
    {
        $user = $this->createUser($email, $username);
        $user->addRole(RoleEnum::ADMIN);

        return $user;
    }

    private function createBannedUser(): User
    {
        $user = $this->createUser();
        $user->addRole(RoleEnum::BANNED);

        return $user;
    }
}
