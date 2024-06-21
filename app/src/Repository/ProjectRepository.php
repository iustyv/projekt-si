<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->orderBy('project.updatedAt', 'DESC');
    }

    /**
     * Query projects user is a member of.
     *
     * @param UserInterface $user User entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByMember(User $user): QueryBuilder
    {
        return $this->queryAll()
            ->innerJoin('project.members', 'members')
            ->andWhere('members = :user')
            ->setParameter('user', $user);
    }

    public function save(Project $project): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($project);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Project $project Project entity
     */
    public function delete(Project $project): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($project);
        $this->_em->flush();
    }

    public function findOneById(int $id): ?Project
    {
        return $this->getOrCreateQueryBuilder()
            ->andWhere('project.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countByManager(User $user): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('project.id'))
            ->where('project.manager = :manager')
            ->setParameter(':manager', $user)
            ->getQuery()
            ->getSingleScalarResult();
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
        return $queryBuilder ?? $this->createQueryBuilder('project');
    }
}
