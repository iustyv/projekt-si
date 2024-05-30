<?php
/**
 * Tag repository.
 */

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TagRepository.
 *
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()->orderBy('tag.updatedAt', 'DESC');
    }

    /**
     * Find tag by title.
     *
     * @param string $title
     *
     * @return Tag|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByTitle(string $title): ?Tag
    {
        return $this->getOrCreateQueryBuilder()
            ->andWhere('tag.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find tag by id.
     *
     * @param int $id
     *
     * @return Tag|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById(int $id): ?Tag
    {
        return $this->getOrCreateQueryBuilder()
            ->andWhere('tag.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Save entity.
     *
     * @param Tag $tag Tag entity
     */
    public function save(Tag $tag): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($tag);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Tag $tag Tag entity
     */
    public function delete(Tag $tag): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($tag);
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
        return $queryBuilder ?? $this->createQueryBuilder('tag');
    }
}
