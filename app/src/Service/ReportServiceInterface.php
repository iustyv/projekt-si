<?php
/**
 * Report service interface.
 */

namespace App\Service;

use App\Entity\Report;
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
    public function getPaginatedList(int $page): PaginationInterface;

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
}
