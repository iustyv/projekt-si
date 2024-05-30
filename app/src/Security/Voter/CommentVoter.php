<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\Report;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public function __construct(private readonly Security $security)
    {
    }

    public const EDIT = 'EDIT';
    private const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Comment;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can edit comment.
     *
     * @param Comment          $comment Comment entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canEdit(Comment $comment, UserInterface $user): bool
    {
        return ($comment->getAuthor() === $user || $this->security->isGranted('ROLE_ADMIN'));
    }

    /**
     * Checks if user can delete comment.
     *
     * @param Comment          $comment Comment entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canDelete(Comment $comment, UserInterface $user): bool
    {
        return ($comment->getAuthor() === $user || $this->security->isGranted('ROLE_ADMIN'));
    }
}