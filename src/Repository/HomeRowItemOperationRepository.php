<?php

namespace App\Repository;

use App\Entity\HomeRowItemOperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HomeRowItemOperation>
 *
 * @method HomeRowItemOperation|null find($id, $lockMode = null, $lockVersion = null)
 * @method HomeRowItemOperation|null findOneBy(array $criteria, array $orderBy = null)
 * @method HomeRowItemOperation[]    findAll()
 * @method HomeRowItemOperation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HomeRowItemOperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HomeRowItemOperation::class);
    }

    /**
     */
    public function add(HomeRowItemOperation $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     */
    public function remove(HomeRowItemOperation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function deleteByHomeRowItem($item_id)
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->delete()
            ->where('e.home_row_item = :item_id')
            ->setParameter('item_id', $item_id);

        $query = $queryBuilder->getQuery();
        return $query->execute();
    }

    // /**
    //  * @return HomeRowItemOperation[] Returns an array of HomeRowItemOperation objects
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
    public function findOneBySomeField($value): ?HomeRowItemOperation
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
