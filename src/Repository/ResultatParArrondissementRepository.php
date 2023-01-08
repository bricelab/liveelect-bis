<?php

namespace App\Repository;

use App\Entity\Arrondissement;
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

    public function tauxParticipationNational(?Arrondissement $arrondissement = null): float
    {
        $qb = $this->createQueryBuilder('r')
            ->select('sum(r.nbInscrits) as nbInscrits', 'sum(r.nbVotants) as nbVotants')
        ;

        if ($arrondissement !== null) {
            $qb->andWhere('r.arrondissement = :arrondissement')->setParameter('arrondissement', $arrondissement);
        }

        $result = $qb->getQuery()->getResult();

        return round($result[0]['nbVotants'] * 100 / $result[0]['nbInscrits'], 2);
    }

    public function tauxVotesNuls(): float
    {
        $result = $this->createQueryBuilder('r')
            ->select('sum(r.nbBulletinsNuls) as nbBulletinsNuls', 'sum(r.nbVotants) as nbVotants')
            ->getQuery()
            ->getResult()
        ;

        return round($result[0]['nbBulletinsNuls'] * 100 / $result[0]['nbVotants'], 2);
    }

    public function nbInscritsEtVotantsParArrondissement(Arrondissement $arrondissement): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('sum(r.nbInscrits) as nbInscrits', 'sum(r.nbVotants) as nbVotants')
            ->andWhere('r.arrondissement = :arrondissement')
            ->setParameter('arrondissement', $arrondissement)
        ;

        $result = $qb->getQuery()->getResult();

        return [
            'nbVotants' => $result[0]['nbVotants'] ?? 0,
            'nbInscrits' => $result[0]['nbInscrits'] ?? 0,
        ];
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
