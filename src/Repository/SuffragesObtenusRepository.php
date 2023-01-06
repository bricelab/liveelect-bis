<?php

namespace App\Repository;

use App\Entity\SuffragesObtenus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SuffragesObtenus>
 *
 * @method SuffragesObtenus|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuffragesObtenus|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuffragesObtenus[]    findAll()
 * @method SuffragesObtenus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuffragesObtenusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuffragesObtenus::class);
    }

    public function save(SuffragesObtenus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SuffragesObtenus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SuffragesObtenus[] Returns an array of SuffragesObtenus objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SuffragesObtenus
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
