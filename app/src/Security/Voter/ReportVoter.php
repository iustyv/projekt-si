<?php
/**
 * Report voter.
 */

namespace App\Security\Voter;

use App\Entity\Enum\ReportStatus;
use App\Entity\Report;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ReportVoter.
 */
class ReportVoter extends Voter
{
    /**
     * Construct.
     *
     * @param Security $security Security
     */
    public function __construct(private readonly Security $security)
    {
    }

    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const CREATE_REPORT = 'CREATE_REPORT';
    private const DELETE = 'DELETE';
    private const COMMENT = 'COMMENT';
    private const TOGGLE_ARCHIVE = 'TOGGLE_ARCHIVE';

    /**
     * Determines if the given attribute and subject are supported by this voter.
     *
     * @param string $attribute The attribute to check
     * @param Report $subject   The subject to check
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE_REPORT, self::EDIT, self::VIEW, self::DELETE, self::COMMENT, self::TOGGLE_ARCHIVE])) {
            return false;
        }

        if (self::CREATE_REPORT === $attribute) {
            return true;
        }

        return $subject instanceof Report;
    }

    /**
     * Determines if the given attribute is granted for the specified subject and user.
     *
     * @param string         $attribute The attribute to be checked
     * @param Report         $subject   The subject to check
     * @param TokenInterface $token     Security token
     *
     * @return bool Result
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (self::VIEW === $attribute) {
            return $this->canView($subject, $user);
        }
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::CREATE_REPORT => $this->canCreate($user),
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            self::COMMENT => $this->canComment($subject, $user),
            self::TOGGLE_ARCHIVE => $this->canToggleArchive($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can create report.
     *
     * @param User $user User
     *
     * @return bool Result
     */
    private function canCreate(UserInterface $user): bool
    {
        if ($user->isBlocked()) {
            return false;
        }

        return $this->security->isGranted('IS_AUTHENTICATED');
    }

    /**
     * Checks if user can edit report.
     *
     * @param Report $report Report entity
     * @param User   $user   User
     *
     * @return bool Result
     */
    private function canEdit(Report $report, User $user): bool
    {
        if (ReportStatus::STATUS_ARCHIVED === $report->getStatus()) {
            return false;
        }
        if ($user->isBlocked()) {
            return false;
        }

        return $user === $report->getAuthor() || $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * Checks if user can view report.
     *
     * @param Report             $report Report entity
     * @param UserInterface|null $user   User
     *
     * @return bool Result
     */
    private function canView(Report $report, ?UserInterface $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $project = $report->getProject();
        if (!$project instanceof \App\Entity\Project) {
            return true;
        }

        return $this->security->isGranted('IS_AUTHENTICATED') && $project->isMemeber($user);
    }

    /**
     * Checks if user can delete report.
     *
     * @param Report $report Report entity
     * @param User   $user   User
     *
     * @return bool Result
     */
    private function canDelete(Report $report, User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($user->isBlocked() || ReportStatus::STATUS_ARCHIVED === $report->getStatus()) {
            return false;
        }

        return $report->getAuthor() === $user;
    }

    /**
     * Checks if user can comment.
     *
     * @param Report $report Report entity
     * @param User   $user   Signed-in user
     *
     * @return bool Result
     */
    private function canComment(Report $report, User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($user->isBlocked() || ReportStatus::STATUS_ARCHIVED === $report->getStatus()) {
            return false;
        }

        return $this->security->isGranted('IS_AUTHENTICATED');
    }

    /**
     * Checks if user can archive report.
     *
     * @param Report        $report Report entity
     * @param UserInterface $user   User
     *
     * @return bool Result
     */
    private function canToggleArchive(Report $report, UserInterface $user): bool
    {
        if ($user->isBlocked()) {
            return false;
        }

        return $user === $report->getAuthor() || $this->security->isGranted('ROLE_ADMIN');
    }
}
