<?php
/**
 * User service.
 */

namespace App\Service;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    /**
     * Constructor.
     *
     * @param UserRepository   $userRepository User repository
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly PaginatorInterface $paginator, private readonly Security $security)
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
     * @param int|null $page   Page number
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
     * Delete entity.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void
    {
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
     * @returns bool result
     */
    public function adminCanBeDeleted(User $user): bool
    {
        return ($user !== $this->security->getUser());
    }

    public function findOneByUsername(string $username): ?User
    {
        return $this->userRepository->findOneByUsername($username);
    }

    public function addRole(User $user, string $role): void
    {
        $user->addRole($role);
    }

    public function toggleBlock(User $user): void
    {
        $user->setIsBlocked(!$user->isBlocked());
    }

}
