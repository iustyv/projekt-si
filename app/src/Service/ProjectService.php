<?php
/**
 * Project service.
 */

namespace App\Service;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ProjectService.
 */
class ProjectService implements ProjectServiceInterface
{
    /**
     * Items per page.
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ProjectRepository   $projectRepository Project repository
     * @param PaginatorInterface $paginator        Paginator
     */
    public function __construct(private readonly ProjectRepository $projectRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int|null $page Page number
     * @param User $user User entity
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(User $user, ?int $page = 1): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->projectRepository->queryByMember($user),
            $page ?? 1,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Project $project Project entity
     */
    public function save(Project $project): void
    {
        $this->projectRepository->save($project);
    }

    /**
     * Delete entity.
     *
     * @param Project $project
     */
    public function delete(Project $project): void
    {
        $this->projectRepository->delete($project);
    }
}
