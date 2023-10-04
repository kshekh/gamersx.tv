<?php

namespace App\Repository;

use App\Entity\HomeRowItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HomeRowItem>
 *
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

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(HomeRowItem $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(HomeRowItem $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findUniqueItem($key,$value,$how_row_item_id = '')
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = "SELECT * FROM home_row_item WHERE JSON_EXTRACT(topic, :key) = :value AND is_unique_container = 0 AND is_published = 1";
        if($how_row_item_id != '') {
            $sql .= " AND id != $how_row_item_id ";
        }
        $statement = $connection->prepare($sql);
        $statement->bindValue('key', '$.' . $key);
        $statement->bindValue('value', $value);
        $statement->execute();

        return $statement->fetchAll();
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
