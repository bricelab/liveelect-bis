<?php

namespace App\Repository;

use App\Entity\VillageQuartier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VillageQuartier>
 *
 * @method VillageQuartier|null find($id, $lockMode = null, $lockVersion = null)
 * @method VillageQuartier|null findOneBy(array $criteria, array $orderBy = null)
 * @method VillageQuartier[]    findAll()
 * @method VillageQuartier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VillageQuartierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VillageQuartier::class);
    }

    public function save(VillageQuartier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VillageQuartier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return VillageQuartier[] Returns an array of VillageQuartier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VillageQuartier
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
