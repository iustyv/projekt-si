<?php
/**
 * Report repository.
 */

namespace App\Repository;

use App\Dto\ReportListFiltersDto;
use App\Entity\Category;
use App\Entity\Enum\ReportStatus;
use App\Entity\Project;
use App\Entity\Report;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
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
     * @param Security        $security Security
     */
    public function __construct(ManagerRegistry $registry, private readonly Security $security)
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
                'partial report.{id, createdAt, updatedAt, title, status, author, project}',
                'partial category.{id, title}',
                'partial tags.{id, title}',
                'partial author.{id, nickname}',
                'partial project.{id, name}',
            )
            ->join('report.category', 'category')
            ->leftJoin('report.tags', 'tags')
            ->join('report.author', 'author')
            ->leftJoin('report.project', 'project')
            ->orderBy('report.updatedAt', 'DESC');
    }

    /**
     * Query reports that user can access.
     *
     * @param array|null           $projects Projects user is a member of
     * @param ReportListFiltersDto $filters  Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryAccessible(?array $projects, ReportListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $queryBuilder = $queryBuilder
                ->andWhere('report.project IS NULL OR report.project IN (:projects)')
                ->setParameter('projects', $projects);
        }

        return $this->applyFiltersToList($queryBuilder, $filters, $projects);
    }

    /**
     * Query tasks by author.
     *
     * @param UserInterface        $user    User entity
     * @param ReportListFiltersDto $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(UserInterface $user, ReportListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

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

    /**
     * Save entity.
     *
     * @param Report $report Report entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
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
     *
     * @throws ORMException
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
     * @param QueryBuilder         $queryBuilder Query builder
     * @param ReportListFiltersDto $filters      Filters
     * @param array|null           $projects     Project user is a member of
     *
     * @return QueryBuilder Query builder
     */
    private function applyFiltersToList(QueryBuilder $queryBuilder, ReportListFiltersDto $filters, ?array $projects): QueryBuilder
    {
        if ($filters->category instanceof Category) {
            $queryBuilder->andWhere('category = :category')
                ->setParameter('category', $filters->category);
        }

        if ($filters->tag instanceof Tag) {
            $queryBuilder->andWhere('tags IN (:tag)')
                ->setParameter('tag', $filters->tag);
        }

        if ($filters->reportStatus instanceof ReportStatus) {
            $queryBuilder->andWhere('report.status = :status_filter')
                ->setParameter('status_filter', $filters->reportStatus->value, Types::INTEGER);
        }

        if ($filters->project instanceof Project && $this->security->isGranted('VIEW', $filters->project)) {
            $queryBuilder->andWhere('report.project = :project')
            ->setParameter('project', $filters->project);
        }

        if (is_string($filters->search)) {
            $queryBuilder->andWhere('report.title LIKE :search')
                ->setParameter('search', '%'.$filters->search.'%');
        }

        if ($filters->unassigned) {
            $queryBuilder->andWhere('report.project IS NULL');
        }

        if ($filters->assigned) {
            $queryBuilder->andWhere('report.project IN (:projects)')
            ->setParameter('projects', $projects);
        }

        if ($this->security->isGranted('ROLE_ADMIN') && $filters->adminAssigned) {
            $queryBuilder->andWhere('report.project NOT IN (:projects)')
                ->setParameter('projects', $projects);
        }

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
