<?php
/**
 * Comment service interface.
 */

namespace App\Service;

use App\Entity\Report;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface CommentServiceInterface.
 */
interface CommentServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param Report $report Report entity
     * @param int    $page   Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(Report $report, int $page): PaginationInterface;
}
