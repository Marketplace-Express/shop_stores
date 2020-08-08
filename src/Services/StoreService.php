<?php
/**
 * User: Wajdi Jurry
 * Date: ٩‏/٥‏/٢٠٢٠
 * Time: ٢:١٣ ص
 */

namespace App\Services;


use App\Entity\Sort\SortStore;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Jurry\RabbitMQ\Handler\RequestSender;

class StoreService
{
    /** @var StoreRepository */
    private $repository;

    /** @var RequestSender */
    private $requestSender;

    /** @var ServiceFactory */
    private $factory;

    /**
     * StoreService constructor.
     * @param EntityManagerInterface $entityManager
     * @param ServiceFactory $factory
     */
    public function __construct(EntityManagerInterface $entityManager, ServiceFactory  $factory)
    {
        $this->repository = $entityManager->getRepository('App:Store');
        $this->factory = $factory;
    }

    /**
     * @param string $ownerId
     * @param string $name
     * @param string $description
     * @param int $type
     * @param string|null $photo
     * @param string|null $coverPhoto
     * @param array $location
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(string $ownerId, string $name, string $description, int $type, ?string $photo, ?string $coverPhoto, array $location = []): array
    {
        return $this->repository->create($ownerId, $name, $description, $type, $photo, $coverPhoto, $location)->toApiArray();
    }

    /**
     * @param string $storeId
     * @param string|null $name
     * @param string|null $description
     * @param string|null $photo
     * @param string|null $coverPhoto
     * @param array|null $location
     * @return array
     * @throws \App\Exception\DisabledEntityException
     * @throws \App\Exception\NotFound
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function update(string $storeId, ?string $name, ?string $description, ?string $photo, ?string $coverPhoto, ?array $location): array
    {
        return $this->repository->update($storeId, $name, $description, $location, $photo, $coverPhoto)->toApiArray();
    }

    /**
     * @param string $storeId
     * @param int $disableReason
     * @param string $disableComment
     * @throws \App\Exception\DisabledEntityException
     * @throws \App\Exception\NotFound
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function disable(string $storeId, int $disableReason, string $disableComment): void
    {
        $this->repository->disable($storeId, $disableReason, $disableComment);
    }

    /**
     * @param string $storeId
     * @param bool $withCategories
     * @return array
     * @throws \App\Exception\DisabledEntityException
     * @throws \App\Exception\NotFound
     * @throws \App\Exception\ServiceNotFoundException
     * @throws \App\Exception\UnableToInvokeException
     */
    public function getById(string $storeId, bool $withCategories = false): array
    {
        $store = $this->repository->getById($storeId)->toApiArray();

        if ($withCategories) {
            $dataGrabber = $this->factory
                ->setServiceName('data_grabber')
                ->setMethod('fetch')
                ->createService();
            $store['categories'] = $dataGrabber('categories_sync', 'categoryService', 'getByStoreId', $storeId);
        }

        return $store;
    }

    /**
     * @param int $page
     * @param int $limit
     * @param SortStore|null $sort
     * @return array
     * @throws \Exception
     */
    public function getAll(int $page, int $limit, ?SortStore $sort): array
    {
        $result = $this->repository->getAll($page, $limit, $sort);
        return array_map(function ($store) {
            return $store->toApiArray();
        }, $result['stores']);
    }

    /**
     * @param string $storeId
     * @throws \App\Exception\DisabledEntityException
     * @throws \App\Exception\NotFound
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(string $storeId)
    {
        $this->repository->delete($storeId);
    }
}