<?php

namespace App\Repository;

use App\Entity\HomeRowItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HomeRowItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method HomeRowItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method HomeRowItem[]    findAll()
 * @method HomeRowItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HomeRowItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HomeRowItem::class);
    }

    // /**
    //  * @return HomeRowItem[] Returns an array of HomeRowItem objects
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
    public function findOneBySomeField($value): ?HomeRowItem
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
