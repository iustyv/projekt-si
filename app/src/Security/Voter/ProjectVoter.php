<?php
/**
 * Project voter.
 */

namespace App\Security\Voter;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ProjectVoter.
 */
class ProjectVoter extends Voter
{
    /**
     * Constructor.
     *
     * @param Security $security Security
     */
    public function __construct(private readonly Security $security)
    {
    }

    private const VIEW = 'VIEW';
    private const EDIT = 'EDIT';
    private const DELETE = 'DELETE'; // TODO add delete method or remove this
    private const CREATE_PROJECT = 'CREATE_PROJECT';

    /**
     * Determines if the given attribute and subject are supported by this voter.
     *
     * @param string  $attribute The attribute to check
     * @param Project $subject   The subject to check
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE, self::CREATE_PROJECT])) {
            return false;
        }

        if (self::CREATE_PROJECT === $attribute) {
            return true;
        }

        return $subject instanceof Project;
    }

    /**
     * Determines if the given attribute is granted for the specified subject and user.
     *
     * @param string         $attribute The attribute to be checked
     * @param Project        $subject   The subject to check
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
            self::VIEW => $this->canView($subject, $user),
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            self::CREATE_PROJECT => $this->canCreate($user),
            default => false,
        };
    }

    /**
     * Checks if user can view project.
     *
     * @param Project       $project Project entity
     * @param UserInterface $user    User
     *
     * @return bool Result
     */
    private function canView(Project $project, UserInterface $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return in_array($user, $project->getMembers()->toArray());
    }

    /**
     * Checks if user can edit project.
     *
     * @param Project $project Project entity
     * @param User    $user    User
     *
     * @return bool Result
     */
    private function canEdit(Project $project, User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return !$user->isBlocked() && $project->getManager() === $user;
    }

    /**
     * Checks if user can delete project.
     *
     * @param Project       $project Project entity
     * @param UserInterface $user    User
     *
     * @return bool Result
     */
    private function canDelete(Project $project, UserInterface $user): bool
    {
        return $project->getManager() === $user || $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * Checks if user can create project.
     *
     * @param User $user User
     *
     * @return bool Result
     */
    private function canCreate(User $user): bool
    {
        return !$user->isBlocked();
    }
}
