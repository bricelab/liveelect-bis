<?php

namespace App\Repository;

use App\Entity\SuperviseurArrondissement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SuperviseurArrondissement>
 *
 * @method SuperviseurArrondissement|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuperviseurArrondissement|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuperviseurArrondissement[]    findAll()
 * @method SuperviseurArrondissement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuperviseurArrondissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuperviseurArrondissement::class);
    }

    public function save(SuperviseurArrondissement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SuperviseurArrondissement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SuperviseurArrondissement[] Returns an array of SuperviseurArrondissement objects
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

//    public function findOneBySomeField($value): ?SuperviseurArrondissement
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
