<?php
/**
 * User: Wajdi Jurry
 * Date: 08/08/2020
 * Time: ٦:٢٢ م
 */

namespace App\Tests\Services;


use App\Services\FollowService;
use App\Services\ServiceFactory;
use App\Services\StoreService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ServiceFactoryTest extends KernelTestCase
{
    private $factory;

    public function setUp()
    {
        self::bootKernel();
        $this->factory = new ServiceFactory(self::$container);
    }

    public function servicesDataProvider()
    {
        return [
            ['follow', 'expected' => FollowService::class],
            ['store', 'expected' => StoreService::class],
        ];
    }

    /**
     * @param $service
     * @param $expected
     * @throws \App\Exception\ServiceNotFoundException
     * @dataProvider servicesDataProvider
     */
    public function testCreateService($service, $expected)
    {
        $this->factory->setServiceName($service);
        $this->assertInstanceOf($expected, $this->factory->createService());
    }
}
