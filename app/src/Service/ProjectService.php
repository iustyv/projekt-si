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
     * @param ProjectRepository  $projectRepository Project repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(private readonly ProjectRepository $projectRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param User     $user User entity
     * @param int|null $page Page number
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
     * @param Project $project Project entity
     */
    public function delete(Project $project): void
    {
        $this->projectRepository->delete($project);
    }

    /**
     * Find project by id.
     *
     * @param int $id Id
     *
     * @return Project|null Project entity
     */
    public function findOneById(int $id): ?Project
    {
        return $this->projectRepository->findOneById($id);
    }

    /**
     * Get projects user is a member of.
     *
     * @param User|null $user User entity
     *
     * @return array Projects user is a member of
     */
    public function getUserProjects(?User $user): array
    {
        if (!$user instanceof User) {
            return [];
        }

        return $this->projectRepository->queryByMember($user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Add members to project.
     *
     * @param Project $project    Project entity
     * @param array   $newMembers Members to be added
     */
    public function addMembers(Project $project, array $newMembers): void
    {
        $project->addMembers($newMembers);
        $this->save($project);
    }
}
