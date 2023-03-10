<?php

namespace App\Repository;

use App\Entity\Arrondissement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Arrondissement>
 *
 * @method Arrondissement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Arrondissement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Arrondissement[]    findAll()
 * @method Arrondissement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArrondissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Arrondissement::class);
    }

    public function save(Arrondissement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Arrondissement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function countAllByDepartement(): array
    {
        $result = $this->createQueryBuilder('a')
            ->select('d.nom', 'count(a.id) as nb_arr')
            ->join('a.commune', 'c')
            ->join('c.departement', 'd')
            ->groupBy('d.id')
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        $data = [];

        foreach ($result as $value) {
            $data[$value['nom']] = $value['nb_arr'];
        }

        return $data;
    }

    public function countAllRemontesByDepartement(): array
    {
        $result = $this->createQueryBuilder('a')
            ->select('d.nom', 'count(a.id) as nb_arr')
            ->join('a.commune', 'c')
            ->join('c.departement', 'd')
            ->andWhere('a.estRemonte = true')
            ->groupBy('d.id')
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;

        $data = [];

        foreach ($result as $value) {
            $data[$value['nom']] = $value['nb_arr'];
        }

        return $data;
    }

//    /**
//     * @return Arrondissement[] Returns an array of Arrondissement objects
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

//    public function findOneBySomeField($value): ?Arrondissement
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
