<?php

namespace App\Security\Voter;

use App\Entity\Enum\ReportStatus;
use App\Entity\Report;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ReportVoter extends Voter
{
    public function __construct(private readonly Security $security)
    {
    }

    public const EDIT_REPORT = 'EDIT_REPORT';
    public const VIEW_REPORT = 'VIEW_REPORT';
    private const DELETE_REPORT = 'DELETE_REPORT';
    private const COMMENT = 'COMMENT';
    private const TOGGLE_ARCHIVE = 'TOGGLE_ARCHIVE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT_REPORT, self::VIEW_REPORT, self::DELETE_REPORT, self::COMMENT, self::TOGGLE_ARCHIVE])
            && $subject instanceof Report;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT_REPORT => $this->canEdit($subject, $user),
            self::VIEW_REPORT => $this->canView($subject, $user),
            self::DELETE_REPORT => $this->canDelete($subject, $user),
            self::COMMENT => $this->canComment($subject, $user),
            self::TOGGLE_ARCHIVE => $this->canToggleArchive($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can edit report.
     *
     * @param Report          $report Report entity
     * @param User $user User
     *
     * @return bool Result
     */
    private function canEdit(Report $report, User $user): bool
    {
        if ($report->getStatus() === ReportStatus::STATUS_ARCHIVED) return false;
        if ($user->isBlocked()) return false;
        return ($user === $report->getAuthor() || $this->security->isGranted('ROLE_ADMIN'));
    }

    /**
     * Checks if user can view report.
     *
     * @param Report          $report Report entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canView(Report $report, UserInterface $user): bool
    {
        return ($report->getAuthor() === $user || $this->security->isGranted('ROLE_ADMIN'));
    }

    /**
     * Checks if user can delete report.
     *
     * @param Report          $report Report entity
     * @param User $user User
     *
     * @return bool Result
     */
    private function canDelete(Report $report, User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) return true;
        if($user->isBlocked() || $report->getStatus() === ReportStatus::STATUS_ARCHIVED) return false;
        return ($report->getAuthor() === $user);
    }

    private function canComment(Report $report, User $user): bool
    {
        return ($this->canDelete($report, $user));
    }

    /**
     * Checks if user can archive report.
     *
     * @param Report          $report Report entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canToggleArchive(Report $report, UserInterface $user): bool
    {
        if ($user->isBlocked()) return false;
        return ($user === $report->getAuthor() || $this->security->isGranted('ROLE_ADMIN'));
    }
}
