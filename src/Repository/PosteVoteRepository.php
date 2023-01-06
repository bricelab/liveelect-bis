<?php

namespace App\Repository;

use App\Entity\Arrondissement;
use App\Entity\PosteVote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PosteVote>
 *
 * @method PosteVote|null find($id, $lockMode = null, $lockVersion = null)
 * @method PosteVote|null findOneBy(array $criteria, array $orderBy = null)
 * @method PosteVote[]    findAll()
 * @method PosteVote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PosteVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PosteVote::class);
    }

    public function save(PosteVote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PosteVote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Arrondissement $arrondissement
     * @return PosteVote[]
     */
    public function findByArrondissement(Arrondissement $arrondissement): array
    {
        return $this->createQueryBuilder('pv')
            ->select('pv')
            ->join('pv.centreVote', 'cv')
            ->join('cv.villageQuartier', 'vq')
            ->andWhere('vq.arrondissement = :arrondissement')
            ->setParameter('arrondissement', $arrondissement)
            ->orderBy('pv.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function countByArrondissement(Arrondissement $arrondissement): int
    {
        return $this->createQueryBuilder('pv')
            ->select('count(pv) as nb_pv')
            ->join('pv.centreVote', 'cv')
            ->join('cv.villageQuartier', 'vq')
            ->andWhere('vq.arrondissement = :arrondissement')
            ->setParameter('arrondissement', $arrondissement)
            ->orderBy('pv.id', 'ASC')
            ->getQuery()
            ->getScalarResult()[0]['nb_pv']
        ;
    }

    public function countByArrondissementAndByRemonteStatus(Arrondissement $arrondissement): int
    {
        return $this->createQueryBuilder('pv')
            ->select('count(pv) as nb_pv')
            ->join('pv.centreVote', 'cv')
            ->join('cv.villageQuartier', 'vq')
            ->andWhere('vq.arrondissement = :arrondissement')
            ->andWhere('pv.estRemonte = :estRemonte')
            ->setParameter('arrondissement', $arrondissement)
            ->setParameter('estRemonte', true)
            ->orderBy('pv.id', 'ASC')
            ->getQuery()
            ->getScalarResult()[0]['nb_pv']
        ;
    }

//    public function findOneBySomeField($value): ?PosteVote
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
