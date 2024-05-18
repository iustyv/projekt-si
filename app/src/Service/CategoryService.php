<?php
/**
 * Category service.
 */

namespace App\Service;


use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ReportRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CategoryService.
 */
class CategoryService implements CategoryServiceInterface
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
     * @param CategoryRepository  $categoryRepository Category repository
     * @param ReportRepository $reportRepository Report repository
     * @param PaginatorInterface $paginator Paginator
     */
    public function __construct(private readonly CategoryRepository $categoryRepository,private readonly ReportRepository $reportRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int|null $page   Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(?int $page = 1): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->categoryRepository->queryAll(),
            $page ?? 1,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated list of reports.
     *
     * @param Category $category Category entity
     * @param int|null $page Page number
     *
     * @return PaginationInterface
     */
    public function getPaginatedListOfReports(Category $category, ?int $page = 1): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->reportRepository->findByCategory($category),
            $page ?? 1,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }


}