<?php
/**
 * Report service.
 */

namespace App\Service;

use App\Entity\Report;
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
    public function __construct(private readonly ReportRepository $reportRepository, private readonly PaginatorInterface $paginator)
    {
    }

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
            $this->reportRepository->queryAll(),
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
}
