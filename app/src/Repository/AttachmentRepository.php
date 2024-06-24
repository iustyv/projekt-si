<?php
/**
 * Attachment service.
 */

namespace App\Repository;

use App\Entity\Attachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class AttachmentRepository.
 *
 * @extends ServiceEntityRepository<Attachment>
 */
class AttachmentRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attachment::class);
    }

    /**
     * Save entity.
     *
     * @param Attachment $attachment Attachment
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Attachment $attachment): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($attachment);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Attachment $attachment Attachment
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Attachment $attachment): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($attachment);
        $this->_em->flush();
    }

    //    /**
    //     * @return Attachment[] Returns an array of Attachment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Attachment
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
