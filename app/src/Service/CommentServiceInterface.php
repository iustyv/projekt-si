<?php
/**
 * Comment service interface.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Report;
use App\Entity\User;
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

    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     */
    public function save(Comment $comment): void;

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void;

    /**
     * Delete comments by report.
     *
     * @param Report $report Report entity
     */
    public function deleteByReport(Report $report): void;

    /**
     * Delete comments by author.
     *
     * @param User $author User entity
     */
    public function deleteByAuthor(User $author): void;
}
