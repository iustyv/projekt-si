<?php
/**
 * Project service interface.
 */

namespace App\Service;

use App\Entity\Project;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface ProjectServiceInterface.
 */
interface ProjectServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param User     $user User entity
     * @param int|null $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(User $user, ?int $page = 1): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Project $project Project entity
     */
    public function save(Project $project): void;

    /**
     * Delete entity.
     *
     * @param Project $project Project entity
     */
    public function delete(Project $project): void;

    /**
     * Find project by id.
     *
     * @param int $id Id
     *
     * @return Project|null Project entity
     */
    public function findOneById(int $id): ?Project;

    /**
     * Get projects user is a member of.
     *
     * @param User|null $user User entity
     *
     * @return array Projects user is a member of
     */
    public function getUserProjects(?User $user): array;

    /**
     * Add members to project.
     *
     * @param Project $project    Project entity
     * @param array   $newMembers Members to be added
     */
    public function addMembers(Project $project, array $newMembers): void;
}
