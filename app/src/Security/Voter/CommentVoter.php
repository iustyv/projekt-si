<?php
/**
 * Comment voter.
 */

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\Enum\ReportStatus;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class CommentVoter.
 */
class CommentVoter extends Voter
{
    /**
     * Constructor.
     *
     * @param Security $security Security
     */
    public function __construct(private readonly Security $security)
    {
    }

    public const EDIT = 'EDIT';
    private const DELETE = 'DELETE';

    /**
     * Determines if the given attribute and subject are supported by this voter.
     *
     * @param string  $attribute The attribute to check
     * @param Comment $subject   The subject to check
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Comment;
    }

    /**
     * Determines if the given attribute is granted for the specified subject and user.
     *
     * @param string         $attribute The attribute to be checked
     * @param Comment        $subject   The subject to check
     * @param TokenInterface $token     Security token
     *
     * @return bool Result
     */
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
     * @param Comment       $comment Comment entity
     * @param UserInterface $user    User
     *
     * @return bool Result
     */
    private function canEdit(Comment $comment, UserInterface $user): bool
    {
        if (ReportStatus::STATUS_ARCHIVED === $comment->getReport()->getStatus()) {
            return false;
        }

        return $comment->getAuthor() === $user || $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * Checks if user can delete comment.
     *
     * @param Comment       $comment Comment entity
     * @param UserInterface $user    User
     *
     * @return bool Result
     */
    private function canDelete(Comment $comment, UserInterface $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        if (ReportStatus::STATUS_ARCHIVED === $comment->getReport()->getStatus()) {
            return false;
        }

        return $comment->getAuthor() === $user;
    }
}
