<?php
/**
 * Project service interface.
 */

namespace App\Service;


use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\ReportRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ProjectServiceInterface.
 */
interface ProjectServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int|null $page Page number
     * @param User $user User entity
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
     * @param Project $project
     */
    public function delete(Project $project): void;

    public function findOneById(int $id): ?Project;

    public function getUserProjects(User $user): array;
}
