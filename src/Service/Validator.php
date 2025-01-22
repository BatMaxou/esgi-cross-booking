<?php

namespace App\Service;

use App\Repository\UserRepository;

class Validator
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function validatePassword(?string $password, ?string $confirm): bool
    {
        return $password && $confirm && $password === $confirm;
    }

    public function validateEmail(string $email): bool
    {
        return false !== filter_var($email, FILTER_VALIDATE_EMAIL)
            && null === $this->userRepository->findOneBy(['email' => $email]);
    }
}
