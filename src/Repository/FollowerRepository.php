<?php

namespace App\Repository;

use App\Entity\Follower;
use App\Entity\Store;
use App\Exception\CantFollowStore;
use App\Repository\Traits\SqlLoggingTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    use SqlLoggingTrait;

    /**
     * FollowerRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->enableLogging($registry);
        parent::__construct($registry, 'App:Follower');
    }

    /**
     * @param Store $store
     * @param string $followerId
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws UniqueConstraintViolationException
     * @throws CantFollowStore
     */
    public function follow(Store $store, string $followerId)
    {
        if ($store->getOwnerId() == $followerId) {
            throw new CantFollowStore('owner try to follow store');
        }
        $follower = new Follower();
        $follower->setFollowerId($followerId);

        $store->addFollower($follower);

        $this->getEntityManager()->persist($store);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Store $store
     * @param string $followerId
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function unFollow(Store $store, string $followerId): void
    {
        $follower = $this->findOneBy(['followerId' => $followerId, 'store' => $store]);

        if (!$follower) {
            return;
        }

        $store->removeFollower($follower);

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

    public function removeFollowers(array $followersIds, string $storeId)
    {
        if (empty($followersIds)) {
            return;
        }

        $this->getEntityManager()->createQueryBuilder()
            ->delete('App:Follower', 'f')
            ->where('f.store = :storeId AND f.followerId IN (:followersIds)')
            ->setParameters(['storeId' => $storeId, 'followersIds' => $followersIds])
            ->getQuery()->getResult();
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
