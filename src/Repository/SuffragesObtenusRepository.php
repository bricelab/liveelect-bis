<?php

namespace App\Repository;

use App\Entity\Circonscription;
use App\Entity\SuffragesObtenus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\ResultSetMapping;
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

    /**
     * @throws Exception
     */
    public function suffragesExprimesParCirconscription(Circonscription $circonscription): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
            SELECT sum(nb_voix) as suffrages_exprimes
FROM `suffrages_obtenus` s
JOIN `resultat_par_arrondissement` r 
JOIN `arrondissement` a 
JOIN `circonscription_arrondissement` ca 
JOIN `circonscription` ci 
ON s.resultat_par_arrondissement_id = r.id AND r.arrondissement_id = a.id AND a.id = ca.arrondissement_id AND ca.circonscription_id = ci.id
WHERE a.est_remonte = 1 AND ci.id = :circonscription
SQL;
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['circonscription' => $circonscription->getId()]);

        return intval($resultSet->fetchAllAssociative()[0]['suffrages_exprimes']);
    }

    /**
     * @throws Exception
     */
    public function suffragesObtenusParCandidatParCirconscription(Circonscription $circonscription): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
            SELECT c.id, c.sigle, sum(nb_voix) as suffrages_obtenus
FROM `suffrages_obtenus` s
JOIN `resultat_par_arrondissement` r 
JOIN `arrondissement` a 
JOIN `circonscription_arrondissement` ca 
JOIN `circonscription` ci 
JOIN `candidat` c 
ON s.resultat_par_arrondissement_id = r.id AND r.arrondissement_id = a.id AND a.id = ca.arrondissement_id AND ca.circonscription_id = ci.id AND s.candidat_id = c.id
WHERE a.est_remonte = 1 AND ci.id = :circonscription
GROUP BY c.id
SQL;
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['circonscription' => $circonscription->getId()]);

        return $resultSet->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function suffragesExprimesNational(): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
            SELECT sum(nb_voix) as suffrages_exprimes
FROM `suffrages_obtenus` s
JOIN `resultat_par_arrondissement` r 
JOIN `arrondissement` a 
JOIN `circonscription_arrondissement` ca 
JOIN `circonscription` ci 
ON s.resultat_par_arrondissement_id = r.id AND r.arrondissement_id = a.id AND a.id = ca.arrondissement_id AND ca.circonscription_id = ci.id
WHERE a.est_remonte = 1
SQL;
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([]);

        return intval($resultSet->fetchAllAssociative()[0]['suffrages_exprimes']);
    }

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
