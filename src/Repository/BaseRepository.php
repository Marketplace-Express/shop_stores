<?php
/**
 * User: Wajdi Jurry
 * Date: ٣‏/٥‏/٢٠٢٠
 * Time: ١٢:٥١ ص
 */

namespace App\Repository;


use App\Logger\DbLogger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\Logger;
use Symfony\Bridge\Doctrine\Logger\DbalLogger;
use Symfony\Component\Stopwatch\Stopwatch;

abstract class BaseRepository extends ServiceEntityRepository
{
    /** @var \Doctrine\DBAL\Logging\DebugStack() */
    private $logger;

    /**
     * BaseRepository constructor.
     * @param ManagerRegistry $registry
     * @param string $entityClass
     */
    public function __construct(ManagerRegistry $registry, $entityClass)
    {
        // Enable DQL debugging in dev environment
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == 'dev') {
            $registry->getConnection()->getConfiguration()->setSQLLogger(
                $this->logger = new DbalLogger(new Logger('queries', [new DbLogger()]), new Stopwatch())
            );
        }

        parent::__construct($registry, $entityClass);
    }
}