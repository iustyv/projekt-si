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
    private const PAGINATOR_ITEMS_PER_PAGE = 6;

    /**
     * Constructor.
     *
     * @param ReportRepository   $reportRepository Report repository
     * @param PaginatorInterface $paginator        Paginator
     */
    public function __construct(private readonly ReportRepository $reportRepository, private readonly PaginatorInterface $paginator, private readonly CategoryServiceInterface $categoryService, private readonly TagServiceInterface $tagService)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int|null $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(ReportListInputFiltersDto $filters, ?int $page = 1): PaginationInterface //TODO add user filter
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->reportRepository->queryActive($filters),
            //$this->reportRepository->queryByAuthor($user, $filters),
            $page ?? 1,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated list of archived reports.
     *
     * @param int|null $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListOfArchived(ReportListInputFiltersDto $filters, ?int $page = 1): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->reportRepository->queryArchived($filters),
            //$this->reportRepository->queryByAuthor($user, $filters),
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
        $this->reportRepository->delete($report);
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
            null !== $filters->categoryId ? $this->categoryService->findOneById($filters->categoryId) : null,
            null !== $filters->tagId ? $this->tagService->findOneById($filters->tagId) : null,
            ReportStatus::tryFrom($filters->statusId)
        );
    }
}
