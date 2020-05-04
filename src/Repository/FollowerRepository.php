<?php

namespace App\Repository;

use App\Entity\Follower;
use App\Entity\Store;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Follower|null find($id, $lockMode = null, $lockVersion = null)
 * @method Follower|null findOneBy(array $criteria, array $orderBy = null)
 * @method Follower[]    findAll()
 * @method Follower[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FollowerRepository extends ServiceEntityRepository
{
    /**
     * FollowerRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Follower::class);
    }

    /**
     * @param Store $store
     * @param string $followerId
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws UniqueConstraintViolationException
     */
    public function follow(Store $store, string $followerId)
    {
        $follower = new Follower();
        $follower->setFollowerId($followerId);

        $store->addFollower($follower);

        $this->getEntityManager()->persist($store);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $followerId
     * @param int $limit
     * @param int $page
     * @return Follower[]|array
     * @throws \Exception
     */
    public function followedStores(string $followerId, int $limit = 10, int $page = 0): array
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('f')
            ->from('App:Follower', 'f')
            ->where('f.followerId = :followerId')
            ->setParameter('followerId', $followerId)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($query);

        return [
            'stores' => $paginator->getIterator()->getArrayCopy(),
            'count' => $paginator->count()
        ];
    }

    /**
     * @param string $storeId
     * @param string $followerId
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function unFollow(string $storeId, string $followerId): void
    {
        $follower = $this->findOneBy(['followerId' => $followerId, 'store' => $storeId]);

        if (!$follower) {
            return;
        }

        $this->getEntityManager()->remove($follower);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $storeId
     * @param int $limit
     * @param int $page
     * @return array
     *
     * @throws \Exception
     */
    public function getFollowers(string $storeId, int $limit = 10, int $page = 1): array
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('f')
            ->from('App:Follower', 'f')
            ->where('f.store = :storeId')
            ->setParameter('storeId', $storeId)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($query);

        return [
            'followers' => $paginator->getIterator()->getArrayCopy(),
            'count' => $paginator->count()
        ];
    }

    // /**
    //  * @return Follower[] Returns an array of Follower objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Follower
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
