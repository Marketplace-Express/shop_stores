<?php
/**
 * User: Wajdi Jurry
 * Date: ١٥‏/٥‏/٢٠٢٠
 * Time: ٣:١٢ ص
 */

namespace App\Repository\Traits;


use App\Logger\DbLogger;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\Logger;
use Symfony\Bridge\Doctrine\Logger\DbalLogger;
use Symfony\Component\Stopwatch\Stopwatch;

trait SqlLoggingTrait
{
    /**
     * @param ManagerRegistry $registry
     */
    public function enableLogging(ManagerRegistry $registry)
    {
        // Enable DQL debugging
        if (isset($_ENV['APP_ENV'])) {
            $registry->getConnection()->getConfiguration()->setSQLLogger(
                new DbalLogger(
                    new Logger('queries', [new DbLogger($_ENV['APP_ENV'])]),
                    new Stopwatch()
                )
            );
        }
    }
}