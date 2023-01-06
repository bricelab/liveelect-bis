<?php

namespace App\Repository;

use App\Entity\ResultatParArrondissement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResultatParArrondissement>
 *
 * @method ResultatParArrondissement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResultatParArrondissement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResultatParArrondissement[]    findAll()
 * @method ResultatParArrondissement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultatParArrondissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResultatParArrondissement::class);
    }

    public function save(ResultatParArrondissement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ResultatParArrondissement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ResultatParArrondissement[] Returns an array of ResultatParArrondissement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ResultatParArrondissement
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
