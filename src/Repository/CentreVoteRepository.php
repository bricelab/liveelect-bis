<?php

namespace App\Repository;

use App\Entity\Arrondissement;
use App\Entity\CentreVote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CentreVote>
 *
 * @method CentreVote|null find($id, $lockMode = null, $lockVersion = null)
 * @method CentreVote|null findOneBy(array $criteria, array $orderBy = null)
 * @method CentreVote[]    findAll()
 * @method CentreVote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CentreVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CentreVote::class);
    }

    public function save(CentreVote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CentreVote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Arrondissement $arrondissement
     * @return CentreVote[]
     */
    public function findByArrondissement(Arrondissement $arrondissement): array
    {
        return $this->createQueryBuilder('cv')
            ->join('cv.villageQuartier', 'vq')
            ->andWhere('vq.arrondissement = :arrondissement')
            ->setParameter('arrondissement', $arrondissement)
            ->orderBy('cv.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return CentreVote[] Returns an array of CentreVote objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CentreVote
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
