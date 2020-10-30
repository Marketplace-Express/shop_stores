<?php
/**
 * User: Wajdi Jurry
 * Date: ١٠‏/٥‏/٢٠٢٠
 * Time: ١:٤٥ ص
 */

namespace App\Entity\EventListeners;


use App\Entity\Store;
use App\Services\ServiceFactory;
use Doctrine\ORM\Event\LifecycleEventArgs;

class StoreEventListener
{
    /**
     * @var ServiceFactory
     */
    private $factory;

    /**
     * StoreEventListener constructor.
     * @param ServiceFactory $factory
     */
    public function __construct(ServiceFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Doctrine\ORM\ORMException
     */
    public function preSoftDelete(LifecycleEventArgs $args)
    {
        $entityManager = $args->getEntityManager();

        /** @var Store $store */
        $store = $args->getObject();
        $store->setDeletionToken(uuid_create(UUID_TYPE_RANDOM));

        $entityManager->persist($store);
        $classMetaData = $entityManager->getClassMetadata(get_class($store));
        $entityManager->getUnitOfWork()->computeChangeSet($classMetaData, $store);
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \App\Exception\ServiceNotFoundException
     */
    public function postSoftDelete(LifecycleEventArgs $args)
    {
        $storeId = $args->getObject()->getStoreId();
        $this->factory
            ->setServiceName('caller')
            ->createService()
            ->callAsync(sprintf('follow/%s/followers', $storeId), 'delete', [], [], []);
    }
}