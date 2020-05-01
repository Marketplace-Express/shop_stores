<?php

namespace App\Repository;

use App\Entity\Location;
use App\Entity\Sort\SortStore;
use App\Entity\Store;
use App\Exception\DisabledEntity;
use App\Exception\NotFound;
use App\Logger\DbLogger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Bridge\Doctrine\Logger\DbalLogger;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @method Store|null find($id, $lockMode = null, $lockVersion = null)
 * @method Store|null findOneBy(array $criteria, array $orderBy = null)
 * @method Store[]    findAll()
 * @method Store[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreRepository extends ServiceEntityRepository
{
    /** @var \Doctrine\DBAL\Logging\DebugStack() */
    private $logger;

    public function __construct(ManagerRegistry $registry)
    {
        // Enable DQL debugging in dev environment
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == 'dev') {
            $registry->getConnection()->getConfiguration()->setSQLLogger(
                $this->logger = new DbalLogger(new Logger('queries', [new DbLogger()]), new Stopwatch())
            );
        }

        parent::__construct($registry, Store::class);
    }

    /**
     * @param string $ownerId
     * @param string $name
     * @param string|null $description
     * @param int $type
     * @param string $photo
     * @param string $coverPhoto
     * @param array $locationData
     * @return Store
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(
        string $ownerId,
        string $name,
        ?string $description,
        int $type,
        string $photo,
        string $coverPhoto,
        ?array $locationData
    )
    {
        $store = new Store();
        $store->setOwnerId($ownerId)
            ->setName($name)
            ->setDescription($description)
            ->setType($type)
            ->setPhoto($photo)
            ->setCoverPhoto($coverPhoto);

        if ($locationData) {
            $location = new Location();
            $location->setCoordinates($locationData['coordinates']);
            $location->setCountry($locationData['country']);
            $location->setCity($locationData['city']);

            $store->setLocation($location);
        }

        $this->getEntityManager()->persist($store);
        $this->getEntityManager()->flush();

        return $store;
    }

    /**
     * @param string $storeId
     * @param bool $getDisabled
     * @return Store|null
     * @throws NotFound
     * @throws DisabledEntity
     */
    public function getById(string $storeId, bool $getDisabled = false): ?Store
    {
        // Whether to get disabled store or not
        $this->getEntityManager()->getConfiguration()->setDefaultQueryHint('withDisabled', $getDisabled);

        /** @var Store|null $store */
        $store = $this->find($storeId);

        if (!$store) {
            throw new NotFound();
        }

        return $store;
    }

    /**
     * @param string $storeId
     * @param int $disableReason
     * @param string|null $disableComment
     * @throws NotFound
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws DisabledEntity
     */
    public function disable(string $storeId, int $disableReason, ?string $disableComment = null)
    {
        $store = $this->getById($storeId);

        $store->setDisableReason($disableReason);
        $store->setDisableComment($disableComment);

        $this->getEntityManager()->persist($store);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $storeId
     * @throws NotFound
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws DisabledEntity
     */
    public function delete(string $storeId)
    {
        $this->getEntityManager()->remove($this->getById($storeId, true));
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $storeId
     * @param string|null $name
     * @param string|null $description
     * @param array|null $locationData
     * @param string|null $photoUrl
     * @param string|null $coverPhotoUrl
     * @return Store|null
     * @throws NotFound
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws DisabledEntity
     */
    public function update(
        string $storeId,
        ?string $name,
        ?string $description,
        ?array $locationData,
        ?string $photoUrl,
        ?string $coverPhotoUrl
    )
    {
        $store = $this->getById($storeId);

        if (!empty($name)) {
            $store->setName($name);
        }

        if (!empty($description)) {
            $store->setDescription($description);
        }

        if (!empty($locationData)) {
            $location = $store->getLocation();
            $location->setCoordinates($locationData['coordinates']);
            $location->setCountry($locationData['country']);
            $location->setCity($locationData['city']);
            $store->setLocation($location);
        }

        if (!empty($photoUrl)) {
            $store->setPhoto($photoUrl);
        }

        if (!empty($coverPhotoUrl)) {
            $store->setCoverPhoto($coverPhotoUrl);
        }

        $this->getEntityManager()->persist($store);
        $this->getEntityManager()->flush();

        return $store;
    }

    /**
     * @param int $page
     * @param int $limit
     * @param SortStore|null $sort
     * @return array
     * @throws \Exception
     */
    public function getAll(int $page = 1 , int $limit = 10, ?SortStore $sort = null): array
    {
        // Get disabled stores
        $this->getEntityManager()->getConfiguration()->setDefaultQueryHint('withDisabled', true);

        $more = false;
        $stores = $this->findBy([], $sort->getSqlSort(), $limit+1, ($page - 1) * $limit);

        if (count($stores) > $limit) {
            array_pop($stores);
            $more = true;
        }

        return [
            'stores' => $stores,
            'more' => $more
        ];
    }

    // /**
    //  * @return Store[] Returns an array of Store objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Store
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
