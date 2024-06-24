<?php
/**
 * Report service interface.
 */

namespace App\Service;

use App\Dto\ReportListInputFiltersDto;
use App\Entity\Report;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface ReportServiceInterface.
 */
interface ReportServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(User $user, ReportListInputFiltersDto $filters, ?int $page = 1): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Report $report Report entity
     */
    public function save(Report $report): void;

    /**
     * Delete entity.
     *
     * @param Report $report
     */
    public function delete(Report $report): void;

    public function deleteByAuthor(User $user): void;

    /**
     * Toggle archive report.
     *
     * @param Report $report
     */
    public function toggle_archive(Report $report): void;
}
