<?php
/**
 * User: Wajdi Jurry
 * Date: ١٤‏/٨‏/٢٠٢٠
 * Time: ١١:٤٦ ص
 */

namespace App\Tests\Controller;

use App\Controller\FollowController;
use App\Services\FollowService;
use App\Tests\Mockups\RequestMock;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FollowControllerTest extends KernelTestCase
{
    const STORE_ID = 'f58031e2-a1bb-11ea-ac38-0242ac120002';
    const USER_ID = '11845af3-db45-11ea-a2b4-0242ac120002';

    private $requestMock;

    public function setUp()
    {
        self::bootKernel();
        $this->requestMock = new RequestMock();
    }

    /**
     * @param $service
     * @return FollowController
     */
    public function getController($service)
    {
        $controller = new FollowController($service);
        $controller->setContainer(self::$container);
        return $controller;
    }

    /**
     * @param array $methods
     * @return \PHPUnit\Framework\MockObject\MockObject|FollowService
     */
    public function getServiceMock(array $methods = [])
    {
        return $this->getMockBuilder(FollowService::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    public function testFollow()
    {
        $serviceMock = $this->getServiceMock(['follow']);
        $serviceMock->expects(self::once())->method('follow')->with(self::STORE_ID, self::USER_ID);

        $this->requestMock->setContent(json_encode(['storeId' => self::STORE_ID, 'followerId' => self::USER_ID]));

        $controller = $this->getController($serviceMock);
        $controller->follow(self::STORE_ID, $this->requestMock);
    }

    public function testGetFollowers()
    {
        $serviceMock = $this->getServiceMock(['getFollowers']);
        $serviceMock->expects(self::once())->method('getFollowers')->with(self::STORE_ID, 10, 1);

        $this->requestMock->limit = 10;
        $this->requestMock->page = 1;

        $controller = $this->getController($serviceMock);
        $controller->getFollowers(self::STORE_ID, $this->requestMock);
    }

    public function testGetFollowedStores()
    {
        $serviceMock = $this->getServiceMock(['getFollowedStores']);
        $serviceMock->expects(self::once())->method('getFollowedStores')->with(self::USER_ID, 10, 1)->willReturn([
            'stores' => [],
            'count' => 0
        ]);

        $this->requestMock->followerId = self::USER_ID;
        $this->requestMock->limit = 10;
        $this->requestMock->page = 1;

        $controller = $this->getController($serviceMock);
        $controller->getFollowedStores($this->requestMock);
    }

    public function testUnfollow()
    {
        $serviceMock = $this->getServiceMock(['unfollow']);
        $serviceMock->expects(self::once())->method('unfollow')->with(self::STORE_ID, self::USER_ID);


        $this->requestMock->setContent(json_encode(['storeId' => self::STORE_ID, 'followerId' => self::USER_ID]));

        $controller = $this->getController($serviceMock);
        $controller->unFollow($this->requestMock);
    }
}
