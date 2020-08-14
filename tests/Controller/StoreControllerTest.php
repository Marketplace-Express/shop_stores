<?php
/**
 * User: Wajdi Jurry
 * Date: ١٤‏/٨‏/٢٠٢٠
 * Time: ٣:٠٠ م
 */

namespace App\Tests\Controller;

use App\Controller\StoreController;
use App\Entity\Sort\SortStore;
use App\Enums\DisableReasonEnum;
use App\Enums\StoreType;
use App\Exception\ValidationFailed;
use App\Services\StoreService;
use App\Tests\Mockups\RequestMock;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StoreControllerTest extends KernelTestCase
{
    const STORE_ID = 'f58031e2-a1bb-11ea-ac38-0242ac120002';
    const USER_ID = '11845af3-db45-11ea-a2b4-0242ac120002';

    private $requestMock;

    public function setUp()
    {
        parent::bootKernel();
        $this->requestMock = new RequestMock();
    }

    public function getController($serviceMock)
    {
        $controller = new StoreController($serviceMock);
        $controller->setContainer(self::$container);

        return $controller;
    }

    /**
     * @param array $methods
     * @return \PHPUnit\Framework\MockObject\MockObject|StoreService
     */
    public function getServiceMock(array $methods = [])
    {
        return $this->getMockBuilder(StoreService::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    public function testDisable()
    {
        $serviceMock = $this->getServiceMock(['disable']);
        $serviceMock->expects(self::once())->method('disable')->with(self::STORE_ID, DisableReasonEnum::REASON_REQUESTED_BY_USER, '');

        $this->requestMock->setContent(json_encode(['disableReason' => DisableReasonEnum::REASON_REQUESTED_BY_USER, 'disableComment' => '']));

        $controller = $this->getController($serviceMock);
        $controller->disable(self::STORE_ID, $this->requestMock);
    }

    public function testGetAll()
    {
        $sort = json_encode(['id' => 'asc']);

        $serviceMock = $this->getServiceMock(['getAll']);
        $serviceMock->expects(self::once())->method('getAll')->with(1, 10, new SortStore($sort));

        $this->requestMock->sort = $sort;
        $this->requestMock->page = 1;
        $this->requestMock->limit = 10;

        $controller = $this->getController($serviceMock);
        $controller->getAll($this->requestMock);
    }

    public function testDelete()
    {
        $serviceMock = $this->getServiceMock(['delete']);
        $serviceMock->expects(self::once())->method('delete')->with(self::STORE_ID);

        $controller = $this->getController($serviceMock);
        $controller->delete(self::STORE_ID);
    }

    public function testCreate()
    {
        $serviceMock = $this->getServiceMock(['create']);
        $serviceMock->expects(self::once())->method('create')->with(
            self::USER_ID,
            'test store',
            'store description',
            StoreType::TYPE_PHYSICAL,
            'https://www.google.com/logo.png',
            'https://www.google.com/logo.png',
            [
                'country' => 'United Kingdom',
                'city' => 'London',
                'coordinates' => [31.5, 32.5]
            ]
        );

        $this->requestMock->setContent(json_encode([
            'ownerId' => self::USER_ID,
            'name' => 'test store',
            'description' => 'store description',
            'type' => StoreType::TYPE_PHYSICAL,
            'photo' => 'https://www.google.com/logo.png',
            'coverPhoto' => 'https://www.google.com/logo.png',
            'location' => [
                'country' => 'United Kingdom',
                'city' => 'London',
                'coordinates' => [31.5, 32.5]
            ]
        ]));

        $controller = $this->getController($serviceMock);
        $controller->create($this->requestMock);
    }

    public function testCreateWithValidationException()
    {
        $serviceMock = $this->getServiceMock(['create']);
        $serviceMock->expects(self::never())->method('create');

        $this->requestMock->setContent(json_encode([
            'ownerId' => self::USER_ID,
            'invalid-user-id',
            null,
            null,
            'wrong-store-type',
            null,
            null,
            [
                'country' => 'United Kingdom',
                'city' => 'London'
            ]
        ]));

        $controller = $this->getController($serviceMock);
        $response = $controller->create($this->requestMock);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('violations', json_decode($response->getContent(), true)['message']);
        $this->assertCount(13, json_decode($response->getContent(), true)['message']['violations']);
    }

    public function testGetById()
    {
        $serviceMock = $this->getServiceMock(['getById']);
        $serviceMock->expects(self::once())->method('getById')->with(self::STORE_ID);

        $controller = $this->getController($serviceMock);
        $controller->getById(self::STORE_ID);
    }

    public function testUpdate()
    {
        $serviceMock = $this->getServiceMock(['update']);
        $serviceMock->expects(self::once())->method('update')->with(
            self::STORE_ID,
            'test store',
            'store description',
            'https://www.google.com/logo.png',
            'https://www.google.com/logo.png',
            [
                'country' => 'United Kingdom',
                'city' => 'London',
                'coordinates' => [31.5, 32.5]
            ]
        );

        $this->requestMock->setContent(json_encode([
            'name' => 'test store',
            'description' => 'store description',
            'photo' => 'https://www.google.com/logo.png',
            'coverPhoto' => 'https://www.google.com/logo.png',
            'location' => [
                'country' => 'United Kingdom',
                'city' => 'London',
                'coordinates' => [31.5, 32.5]
            ]
        ]));

        $controller = $this->getController($serviceMock);
        $controller->update(self::STORE_ID, $this->requestMock);
    }
}
