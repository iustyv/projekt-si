<?php
/**
 * Report repository.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ReportRepository.
 *
 * @extends ServiceEntityRepository<Report>
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 */
class ReportRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial report.{id, createdAt, updatedAt, title}',
                'partial category.{id, title}'
            )
            ->join('report.category', 'category')
            ->orderBy('report.updatedAt', 'DESC');
    }

    /**
     * Find reports by category.
     *
     * @param Category $category Category entity
     *
     * @return QueryBuilder QueryBuilder
     */
    public function findByCategory(Category $category): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->andWhere('report.category = :category')
            ->setParameter('category', $category)
            ->orderBy('report.updatedAt', 'DESC');
    }

    public function save(Report $report): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($report);
        $this->_em->flush();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('report');
    }
}
