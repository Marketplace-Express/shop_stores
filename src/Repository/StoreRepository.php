<?php

namespace App\Repository;

use App\Entity\Location;
use App\Entity\Store;
use App\Exception\NotFound;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Store|null find($id, $lockMode = null, $lockVersion = null)
 * @method Store|null findOneBy(array $criteria, array $orderBy = null)
 * @method Store[]    findAll()
 * @method Store[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
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
        array $locationData = []
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
     * @return Store|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws NotFound
     */
    public function getById(string $storeId): ?Store
    {
        $store = $this->getEntityManager()->find($this->getClassName(), $storeId);

        if (!$store) {
            throw new NotFound();
        }

        return $store;
    }

    /**
     * @param string $storeId
     * @throws NotFound
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function delete(string $storeId)
    {
        $this->getEntityManager()->remove($this->getById($storeId));
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
