<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Enum\VoterRoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, null>
 */
final class AdminVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            VoterRoleEnum::ADMIN->value,
            VoterRoleEnum::BANNED->value,
            VoterRoleEnum::UNBANED->value,
        ]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            VoterRoleEnum::BANNED->value => $user->isBanned(),
            VoterRoleEnum::UNBANED->value => !$user->isBanned(),
            VoterRoleEnum::ADMIN->value => $user->isAdmin() && !$user->isBanned(),
            default => throw new \LogicException(sprintf('Unknown attribute %s', $attribute)),
        };
    }
}
