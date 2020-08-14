<?php
/**
 * User: Wajdi Jurry
 * Date: 15/05/2020
 * Time: ١:٣٠ ص
 */

namespace App\Services;


use App\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceFactory
{
    /** @var string */
    private $serviceName;

    /** @var ContainerInterface */
    private $container;

    /**
     * ServiceFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $serviceName
     * @return ServiceFactory
     */
    public function setServiceName($serviceName): self
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    public function getServiceName(): string
    {
        return 'app.' . $this->serviceName . '_service';
    }

    /**
     * @return mixed
     *
     * @throws ServiceNotFoundException
     */
    public function createService()
    {
        $serviceName = $this->getServiceName();

        if (!$this->container->has($serviceName)) {
            throw new ServiceNotFoundException($serviceName);
        }

        return $this->container->get($serviceName);
    }
}