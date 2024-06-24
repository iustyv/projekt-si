<?php
/**
 * User service.
 */

namespace App\Service;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    /**
     * Constructor.
     *
     * @param UserRepository          $userRepository    User repository
     * @param PaginatorInterface      $paginator         Paginatory interface
     * @param Security                $security          Security
     * @param ReportServiceInterface  $reportService     Report service interface
     * @param ProjectRepository       $projectRepository Project repository
     * @param CommentServiceInterface $commentService    Comment service interface
     * @param TokenStorageInterface   $tokenStorage      Token storage interface
     * @param UserProviderInterface   $userProvider      User provider interface
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly PaginatorInterface $paginator, private readonly Security $security, private readonly ReportServiceInterface $reportService, private readonly ProjectRepository $projectRepository, private readonly CommentServiceInterface $commentService, private readonly TokenStorageInterface $tokenStorage, private readonly UserProviderInterface $userProvider)
    {
    }

    /**
     * Items per page.
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Get paginated list.
     *
     * @param int|null $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(?int $page = 1): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page ?? 1,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Checks if user can be deleted.
     *
     * @param User $user User entity
     *
     * @return bool Result
     */
    public function userCanBeDeleted(User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN') && !$this->adminCanBeDeleted($user)) {
            return false;
        }

        try {
            $result = $this->projectRepository->countByManager($user);

            return $result <= 0;
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }

    /**
     * Checks if user is signed in.
     *
     * @param User $user User entity
     *
     * @return bool Result
     */
    public function isSignedIn(User $user): bool
    {
        return $user->getId() === $this->security->getUser()->getId();
    }

    /**
     * Delete entity.
     *
     * @param User    $user    User entity
     * @param Request $request HTTP Response
     */
    public function delete(User $user, Request $request): void
    {
        if ($this->isSignedIn($user)) {
            $this->tokenStorage->setToken(null);
            $request->getSession()->invalidate();
        }

        $this->commentService->deleteByAuthor($user);
        $this->reportService->deleteByAuthor($user);
        $this->userRepository->delete($user);
    }

    /**
     * Remove admin.
     *
     * @param User $user User entity
     */
    public function removeAdmin(User $user): void
    {
        $user->setRoles([UserRole::ROLE_USER->value]);
        $this->save($user);
    }

    /**
     * Checks if admin can be deleted.
     *
     * @param User $user User entity
     *
     * @return bool result
     */
    public function adminCanBeDeleted(User $user): bool
    {
        return $user !== $this->security->getUser();
    }

    /**
     * Find user by username.
     *
     * @param string $username Username
     *
     * @return User|null User entity
     */
    public function findOneByUsername(string $username): ?User
    {
        return $this->userRepository->findOneByUsername($username);
    }

    /**
     * Add role.
     *
     * @param User   $user User entity
     * @param string $role Role
     */
    public function addRole(User $user, string $role): void
    {
        $user->addRole($role);
    }

    /**
     * Toggle block.
     *
     * @param User $user user entity
     */
    public function toggleBlock(User $user): void
    {
        $user->setIsBlocked(!$user->isBlocked());
    }

    /**
     * Refresh user token.
     *
     * @param User $user user entity
     */
    public function refreshUserToken(User $user): void
    {
        $this->userProvider->refreshUser($user);
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);
    }
}
