<?php
/**
 * User: Wajdi Jurry
 * Date: ١٥‏/٥‏/٢٠٢٠
 * Time: ١:٣٠ ص
 */

namespace App\Services;


use App\Exception\ServiceNotFoundException;
use App\Exception\UnableToInvokeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceFactory
{
    /** @var string */
    private $serviceName;

    /** @var string */
    private $method;

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
     * @param mixed $method
     * @return ServiceFactory
     */
    public function setMethod($method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return mixed
     *
     * @throws ServiceNotFoundException
     * @throws UnableToInvokeException
     */
    public function createService(): array
    {
        $serviceName = $this->getServiceName();

        if (!$this->container->has($serviceName)) {
            throw new ServiceNotFoundException($serviceName);
        }

        $service = $this->container->get($serviceName);

        if (!is_callable([$service, $this->method])) {
            throw new UnableToInvokeException();
        }

        return [$service, $this->method];
    }
}