<?php
/**
 * User: Wajdi Jurry
 * Date: ١٠‏/٨‏/٢٠٢٠
 * Time: ١٠:٣٦ م
 */

namespace App\Tests\Services;

use App\Entity\Store;
use App\Repository\FollowerRepository;
use App\Repository\StoreRepository;
use App\Services\FollowService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FollowServiceTest extends KernelTestCase
{
    const STORE_ID = 'f58031e2-a1bb-11ea-ac38-0242ac120002';
    const USER_ID = '11845af3-db45-11ea-a2b4-0242ac120002';

    /**
     * @param $storeRepositoryMock
     * @param $followerRepositoryMock
     * @return FollowService
     */
    public function getFollowService($followerRepositoryMock, $storeRepositoryMock = null)
    {
        $entityManagerMock = $this->getEntityManagerMock(['getRepository']);
        $entityManagerMock->expects(self::any())->method('getRepository')->willReturnMap([
            ['App:Follower', $followerRepositoryMock],
            ['App:Store', $storeRepositoryMock]
        ]);

        return new FollowService($entityManagerMock);
    }

    /**
     * @param array $methods
     * @return \PHPUnit\Framework\MockObject\MockObject|EntityManagerInterface
     */
    public function getEntityManagerMock(array $methods = [])
    {
        return $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * @param array $methods
     * @return \PHPUnit\Framework\MockObject\MockObject|StoreRepository
     */
    public function getStoreRepositoryMock(array $methods = [])
    {
        return $this->getMockBuilder(StoreRepository::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * @param array $methods
     * @return \PHPUnit\Framework\MockObject\MockObject|FollowerRepository
     */
    public function getFollowRepositoryMock(array $methods = [])
    {
        return $this->getMockBuilder(FollowerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    public function testFollow()
    {
        $store = new Store();
        $store->setStoreId(self::STORE_ID);

        $storeRepositoryMock = $this->getStoreRepositoryMock(['getById']);
        $storeRepositoryMock->expects(self::once())->method('getById')->with(self::STORE_ID)->willReturn($store);

        $followerRepositoryMock = $this->getFollowRepositoryMock(['follow']);
        $followerRepositoryMock->expects(self::once())->method('follow')->with($store, self::USER_ID);

        $followServiceMock = $this->getFollowService($followerRepositoryMock, $storeRepositoryMock);
        $followServiceMock->follow(self::STORE_ID, self::USER_ID);
    }

    public function testGetFollowedStores()
    {
        $followerRepositoryMock = $this->getFollowRepositoryMock(['followedStores']);
        $followerRepositoryMock->expects(self::once())->method('followedStores')->with(self::USER_ID, 10, 1)->willReturn([
            'stores' => [],
            'count' => 0
        ]);

        $followServiceMock = $this->getFollowService($followerRepositoryMock);
        $followServiceMock->getFollowedStores(self::USER_ID, 10, 1);
    }

    public function testGetFollowers()
    {
        $followerRepositoryMock = $this->getFollowRepositoryMock(['getFollowers']);
        $followerRepositoryMock->expects(self::once())->method('getFollowers')->with(self::STORE_ID, 10, 1)->willReturn([
            'followers' => [],
            'count' => 0
        ]);

        $followServiceMock = $this->getFollowService($followerRepositoryMock);
        $followServiceMock->getFollowers(self::STORE_ID, 10, 1);
    }

    public function testUnFollow()
    {
        $followerRepositoryMock = $this->getFollowRepositoryMock(['unfollow']);
        $followerRepositoryMock->expects(self::once())->method('unfollow')->with(self::STORE_ID, self::USER_ID);

        $followServiceMock = $this->getFollowService($followerRepositoryMock);
        $followerRepositoryMock->unFollow(self::STORE_ID, self::USER_ID);
    }
}
