<?php
/**
 * Report repository.
 */

namespace App\Repository;

use App\Dto\ReportListFiltersDto;
use App\Entity\Category;
use App\Entity\Report;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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
    public function queryAll(ReportListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->select(
                'partial report.{id, createdAt, updatedAt, title}',
                'partial category.{id, title}',
                'partial tags.{id, title}'
            )
            ->join('report.category', 'category')
            ->leftJoin('report.tags', 'tags')
            ->orderBy('report.updatedAt', 'DESC');

        return $this->applyFiltersToList($queryBuilder, $filters);
    }

    /**
     * Query tasks by author.
     *
     * @param UserInterface      $user    User entity
     * @param ReportListFiltersDto $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(UserInterface $user, ReportListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->queryAll($filters);

        $queryBuilder->andWhere('report.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
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

    /**
     * Count reports by category.
     *
     * @param Category $category Category
     *
     * @return int Number of reports in category
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('report.id'))
            ->where('report.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function save(Report $report): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($report);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Report $report Report entity
     */
    public function delete(Report $report): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($report);
        $this->_em->flush();
    }

    /**
     * Apply filters to paginated list.
     *
     * @param QueryBuilder       $queryBuilder Query builder
     * @param TaskListFiltersDto $filters      Filters
     *
     * @return QueryBuilder Query builder
     */
    private function applyFiltersToList(QueryBuilder $queryBuilder, ReportListFiltersDto $filters): QueryBuilder
    {
        if ($filters->category instanceof Category) {
            $queryBuilder->andWhere('category = :category')
                ->setParameter('category', $filters->category);
        }

        if ($filters->tag instanceof Tag) {
            $queryBuilder->andWhere('tags IN (:tag)')
                ->setParameter('tag', $filters->tag);
        }

        /*if ($filters->reportStatus instanceof TaskStatus) {
            $queryBuilder->andWhere('report.status = :status')
                ->setParameter('status', $filters->reportStatus->value, Types::INTEGER);
        }*/

        return $queryBuilder;
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
