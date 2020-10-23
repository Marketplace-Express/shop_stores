<?php
/**
 * User: Wajdi Jurry
 * Date: ٣‏/٥‏/٢٠٢٠
 * Time: ١٢:٥١ ص
 */

namespace App\Repository;


use App\Entity\Location;
use App\Entity\Sort\SortStore;
use App\Entity\Store;
use App\Exception\DisabledEntityException;
use App\Exception\NotFound;
use App\Repository\Traits\SqlLoggingTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Store|null find($id, $lockMode = null, $lockVersion = null)
 * @method Store|null findOneBy(array $criteria, array $orderBy = null)
 * @method Store[]    findAll()
 * @method Store[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreRepository extends ServiceEntityRepository
{

    use SqlLoggingTrait;

    /**
     * StoreRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->enableLogging($registry);
        parent::__construct($registry, 'App:Store');
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
     * @return Store
     * @throws NotFound
     * @throws DisabledEntityException
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
     * @throws DisabledEntityException
     */
    public function disable(string $storeId, int $disableReason, ?string $disableComment = null)
    {
        $store = $this->getById($storeId);

        $store->setDisableReason($disableReason);
        $store->setDisableComment($disableComment);

        $this->getEntityManager()->getEventManager()->dispatchEvent(
            'preDisable', new LifecycleEventArgs($store, $this->getEntityManager())
        );

        $this->getEntityManager()->persist($store);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $storeId
     * @throws NotFound
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws DisabledEntityException
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
     * @return Store
     * @throws NotFound
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws DisabledEntityException
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

        $query = $this->getEntityManager()->createQueryBuilder()
            ->from('App:Store', 's')
            ->select('s')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        if ($sort) {
            $query->orderBy('s.name', 'DESC');
        }

        $paginator = new Paginator($query);

        if ($paginator->count() > $limit) {
            $more = true;
        }

        return [
            'stores' => $paginator->getIterator()->getArrayCopy(),
            'count' => $paginator->count(),
            'more' => !empty($more)
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
