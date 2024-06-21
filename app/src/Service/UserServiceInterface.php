<?php

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface UserServiceInterface
{

    /**
     * Get paginated list.
     *
     * @param int|null $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(?int $page = 1): PaginationInterface;

    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;

    /**
     * Checks if user can be deleted.
     *
     * @param User $user User entity
     *
     * @return bool Result
     */
    public function userCanBeDeleted(User $user): bool;

    /**
     * Checks if user is signed in.
     *
     * @param User $user User entity
     *
     * @return bool Result
     */
    public function isSignedIn(User $user): bool;

    /**
     * Delete entity.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void;

    /**
     * Checks if admin can be deleted.
     *
     * @param User $user User entity
     *
     * @returns bool result
     */
    public function adminCanBeDeleted(User $user): bool;

    /**
     * Remove admin.
     *
     * @param User $user User entity
     */
    public function removeAdmin(User $user): void;

    /**
     * Find user by username.
     *
     * @param string $username
     *
     * @return User|null
     */
    public function findOneByUsername(string $username): ?User;

    public function addRole(User $user, string $role): void;

    public function toggleBlock(User $user): void;
}
