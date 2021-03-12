<?php

namespace App\Repository;

use App\Entity\HomeRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HomeRow|null find($id, $lockMode = null, $lockVersion = null)
 * @method HomeRow|null findOneBy(array $criteria, array $orderBy = null)
 * @method HomeRow[]    findAll()
 * @method HomeRow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HomeRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HomeRow::class);
    }

    // /**
    //  * @return HomeRow[] Returns an array of HomeRow objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HomeRow
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
