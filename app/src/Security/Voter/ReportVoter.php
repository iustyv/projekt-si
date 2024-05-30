<?php

namespace App\Security\Voter;

use App\Entity\Report;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ReportVoter extends Voter
{
    public function __construct(private readonly Security $security)
    {
    }

    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    private const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
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
            self::EDIT => $this->canEdit($subject, $user),
            self::VIEW => $this->canView($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can edit report.
     *
     * @param Report          $report Report entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canEdit(Report $report, UserInterface $user): bool
    {
        return ($report->getAuthor() === $user || $this->security->isGranted('ROLE_ADMIN'));
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
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canDelete(Report $report, UserInterface $user): bool
    {
        return ($report->getAuthor() === $user || $this->security->isGranted('ROLE_ADMIN'));
    }
}
