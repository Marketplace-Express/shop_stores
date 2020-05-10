<?php
/**
 * User: Wajdi Jurry
 * Date: ١٠‏/٥‏/٢٠٢٠
 * Time: ١:٤٥ ص
 */

namespace App\Entity\EventListeners;


use App\Entity\Store;
use Doctrine\ORM\Event\LifecycleEventArgs;

class StoreEventListener
{
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
}