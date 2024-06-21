<?php
/**
 * Comment service.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Report;
use App\Entity\User;
use App\Repository\CommentRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CommentService.
 */
class CommentService implements CommentServiceInterface
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
     * @param CommentRepository  $commentRepository Comment repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(private readonly CommentRepository $commentRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param Report   $report Report entity
     * @param int|null $page   Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(Report $report, ?int $page = 1): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->commentRepository->findByReport($report),
            $page ?? 1,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     */
    public function save(Comment $comment): void
    {
        $this->commentRepository->save($comment);
    }

    /**
     * Delete entity.
     *
     * @param Comment $comment
     */
    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }

    /**
     * Delete comments by report.
     *
     * @param Report $report Report entity
     *
     * @return void
     */
    public function deleteByReport(Report $report): void
    {
        $comments = $this->commentRepository->findBy(['report' => $report]);
        foreach ($comments as $comment) {
            $this->commentRepository->delete($comment);
        }
    }

    /**
     * Delete comments by author.
     *
     * @param User $author User entity
     *
     * @return void
     */
    public function deleteByAuthor(User $author): void
    {
        $comments = $this->commentRepository->findBy(['author' => $author]);
        foreach ($comments as $comment) {
            $this->commentRepository->delete($comment);
        }
    }
}
