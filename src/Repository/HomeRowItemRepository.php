<?php

namespace App\Repository;

use App\Entity\HomeRowItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use Doctrine\ORM\NonUniqueResultException;
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
     */
    public function add(HomeRowItem $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     */
    public function remove(HomeRowItem $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws Exception
     * @throws NonUniqueResultException
     */
    public function findUniqueItem($key, $value, $how_row_item_id = ''): string|array|int|float
    {
//        $entityManager = $this->getEntityManager();
        $connection = $this->getEntityManager()->getConnection();

//        $qb = $this->getEntityManager()->createQueryBuilder()
//            ->select('hri.id')
//            ->from(HomeRowItem::class, 'hri')
//            ->where("JSON_EXTRACT('hri.topic', '$." . $key . "') = :value")
//            ->andWhere('hri.is_unique_container = 0')
//            ->setParameter('value', $value);
//        dd($qb->getQuery()->getResult());
//        return $qb->getQuery()->getSingleColumnResult();

        $sql = '
                SELECT * FROM home_row_item
                WHERE JSON_EXTRACT(topic, :key) = :value
                AND is_unique_container = 0 AND is_published = 1
               ';

        if($how_row_item_id != '') {
            $sql .= " AND id != $how_row_item_id ";
        }
        $statement = $connection->executeQuery($sql, [
            'key' => '$.' . $key,
            'value' => $value
        ]);

        return $statement->fetchFirstColumn();
    }

    public function findStreamer()
    {
        $qb =  $this->createQueryBuilder('h');
            $qb->where('h.isPublished = 1')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('h.itemType',':itemTypeYoutube'),
                    $qb->expr()->eq('h.itemType',':itemTypeStreamer')
                )
            )
            ->setParameter(':itemTypeYoutube', 'youtube')
            ->setParameter(':itemTypeStreamer', 'streamer')
            ;

        return $qb->getQuery()->getResult();
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
