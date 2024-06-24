<?php
/**
 * Report service.
 */

namespace App\Service;

use App\Dto\ReportListFiltersDto;
use App\Dto\ReportListInputFiltersDto;
use App\Entity\Enum\ReportStatus;
use App\Entity\Report;
use App\Entity\User;
use App\Repository\ReportRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Class ReportService.
 */
class ReportService implements ReportServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ReportRepository   $reportRepository Report repository
     * @param PaginatorInterface $paginator        Paginator
     */
    public function __construct(private readonly ReportRepository $reportRepository, private readonly PaginatorInterface $paginator, private readonly CategoryServiceInterface $categoryService, private readonly TagServiceInterface $tagService, private readonly ProjectServiceInterface $projectService, private readonly CommentServiceInterface $commentService)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int|null $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(?User $user, ReportListInputFiltersDto $filters, ?int $page = 1): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);
        $projects = $this->projectService->getUserProjects($user);

        return $this->paginator->paginate(
            $this->reportRepository->queryAccessible($projects, $filters),
            $page ?? 1,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Report $report Report entity
     */
    public function save(Report $report): void
    {
        $this->reportRepository->save($report);
    }

    /**
     * Delete entity.
     *
     * @param Report $report
     */
    public function delete(Report $report): void
    {
        $this->commentService->deleteByReport($report);
        $this->reportRepository->delete($report);
    }

    public function deleteByAuthor(User $user): void
    {
        $reports = $this->reportRepository->findBy(['author' => $user]);
        foreach ($reports as $report) {
            $this->reportRepository->delete($report);
        }
    }

    /**
     * Archive report.
     *
     * @param Report $report
     */
    public function toggle_archive(Report $report): void
    {
        $status = $report->getStatus() === ReportStatus::STATUS_ARCHIVED ? ReportStatus::STATUS_COMPLETED : ReportStatus::STATUS_ARCHIVED;
        $report->setStatus($status);
        $this->save($report);
    }

    /**
     * Prepare filters for the reports list.
     *
     * @param ReportListInputFiltersDto $filters Raw filters from request
     *
     * @return ReportListFiltersDto Result filters
     */
    private function prepareFilters(ReportListInputFiltersDto $filters): ReportListFiltersDto
    {
        return new ReportListFiltersDto(
            $filters->search ?? null,
            null !== $filters->categoryId ? $this->categoryService->findOneById($filters->categoryId) : null,
            null !== $filters->tagId ? $this->tagService->findOneById($filters->tagId) : null,
            ReportStatus::tryFrom($filters->statusId),
            null !== $filters->projectId ? $this->projectService->findOneById($filters->projectId) : null,
            $filters->unassigned ?? false,
            $filters->assigned ?? false,
            $filters->adminAssigned ?? false,
        );
    }
}
