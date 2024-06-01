<?php

namespace App\Security\Voter;

use App\Entity\Project;
use App\Entity\Enum\ReportStatus;
use App\Entity\Report;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter extends Voter
{
    public function __construct(private readonly Security $security)
    {
    }

    private const EDIT_PROJECT = 'EDIT_PROJECT';
    private const DELETE_PROJECT = 'DELETE_PROJECT';
    private const CREATE_PROJECT = 'CREATE_PROJECT';


    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT_PROJECT, self::DELETE_PROJECT, self::CREATE_PROJECT])) return false;

        if ($attribute === self::CREATE_PROJECT) return true;

        return $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT_PROJECT => $this->canEdit($subject, $user),
            self::DELETE_PROJECT => $this->canDelete($subject, $user),
            self::CREATE_PROJECT => $this->canCreate($user),
            default => false,
        };
    }

    /**
     * Checks if user can edit project.
     *
     * @param Project          $project Project entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canEdit(Project $project, UserInterface $user): bool
    {
        return ($project->getManager() === $user || $this->security->isGranted('ROLE_ADMIN'));
    }

    /**
     * Checks if user can delete project.
     *
     * @param Project          $project Project entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canDelete(Project $project, UserInterface $user): bool
    {
        return ($project->getManager() === $user || $this->security->isGranted('ROLE_ADMIN'));
    }

    private function canCreate(UserInterface $user): bool
    {
        return ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED'));
    }
}
