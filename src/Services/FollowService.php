<?php
/**
 * User: Wajdi Jurry
 * Date: ١٠‏/٥‏/٢٠٢٠
 * Time: ١٠:٥٨ م
 */

namespace App\Services;


use App\Repository\FollowerRepository;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;

class FollowService
{
    /** @var FollowerRepository */
    private $followerRepository;

    /** @var StoreRepository */
    private $storeRepository;

    /**
     * FollowService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->followerRepository = $entityManager->getRepository('App:Follower');
        $this->storeRepository = $entityManager->getRepository('App:Store');
    }

    /**
     * @param string $storeId
     * @param string $followerId
     * @throws \App\Exception\DisabledEntityException
     * @throws \App\Exception\NotFound
     * @throws \Doctrine\DBAL\Exception\UniqueConstraintViolationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function follow(string $storeId, string $followerId): void
    {
        $store = $this->storeRepository->getById($storeId);
        $this->followerRepository->follow($store, $followerId);
    }

    /**
     * @param string $storeId
     * @param int $limit
     * @param int $page
     * @return array
     * @throws \Exception
     */
    public function getFollowers(string $storeId, int $limit, int $page): array
    {
        $followers = $this->followerRepository->getFollowers($storeId, $limit, $page);
        $followers['followers'] = array_map(function ($follower) {
            return $follower->getFollowerId();
        }, $followers['followers']);

        return $followers;
    }

    /**
     * @param string $followerId
     * @param int $limit
     * @param int $page
     * @return array
     * @throws \Exception
     */
    public function getFollowedStores(string $followerId, int $limit, int $page): array
    {
        $followedStores = $this->followerRepository->followedStores($followerId, $limit, $page);

        $followedStores['stores'] = array_map(function ($store) {
            return $store->getStore()->toApiArray();
        }, $followedStores['stores']);

        return $followedStores;
    }

    /**
     * @param string $storeId
     * @param string $followerId
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function unFollow(string $storeId, string $followerId): void
    {
        $store = $this->storeRepository->getById($storeId);
        $this->followerRepository->unFollow($store, $followerId);
    }
}