<?php

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;


interface UserServiceInterface
{

    /**
     * Get paginated list.
     *
     * @param int|null $page   Page number
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
}
