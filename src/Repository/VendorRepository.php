<?php

namespace App\Repository;

use App\Entity\Vendor;
use App\Exception\NotFound;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vendor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vendor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vendor[]    findAll()
 * @method Vendor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VendorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vendor::class);
    }

    /**
     * @param string $ownerId
     * @return Vendor
     * @throws \Doctrine\ORM\ORMException
     */
    public function create(string $ownerId): Vendor
    {
        $model = new Vendor();
        $model->setOwnerId($ownerId);
        $this->getEntityManager()->persist($model);
        $this->getEntityManager()->flush();

        return $model;
    }

    /**
     * @param string $vendorId
     * @param string $ownerId
     * @return bool
     * @throws NotFound
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete(string $vendorId, string $ownerId = null)
    {
        $vendor = $this->find($vendorId);

        if (!$vendor) {
            throw new NotFound();
        }

        $this->getEntityManager()->remove($vendor);
        $this->getEntityManager()->flush();

        return true;
    }

    // /**
    //  * @return X[] Returns an array of X objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('x')
            ->andWhere('x.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('x.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?X
    {
        return $this->createQueryBuilder('x')
            ->andWhere('x.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
