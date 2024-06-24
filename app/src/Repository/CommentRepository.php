<?php
/**
 * Comment repository.
 */

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class CommentRepository.
 *
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Find all comments to report.
     *
     * @param Report $report Report entity
     *
     * @return array Comments
     */
    public function findByReport(Report $report): array
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial comment.{id, createdAt, author, content}',
                'partial author.{id, nickname}'
            )
            ->join('comment.author', 'author')
            ->andWhere('comment.report = :report')
            ->setParameter('report', $report)
            ->orderBy('comment.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     *
     * @throws ORMException
     */
    public function save(Comment $comment): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($comment);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     *
     * @throws ORMException
     */
    public function delete(Comment $comment): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($comment);
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
        return $queryBuilder ?? $this->createQueryBuilder('comment');
    }
}
