<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public function __construct(private readonly Security $security)
    {
    }

    public const EDIT_USER = 'EDIT_USER';
    private const DELETE_USER = 'DELETE_USER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT_USER, self::DELETE_USER])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT_USER => $this->canEdit($subject, $user),
            self::DELETE_USER => $this->canDelete($subject, $user),
            default => false,
        };
    }

    private function canEdit(UserInterface $userEdit, UserInterface $user): bool
    {
        return ($userEdit === $user || $this->security->isGranted('ROLE_ADMIN'));
    }

    private function canDelete(UserInterface $userDelete, UserInterface $user): bool
    {
        return ($userDelete === $user || $this->security->isGranted('ROLE_ADMIN'));
    }
}
