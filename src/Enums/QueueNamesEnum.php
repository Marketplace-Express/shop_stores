<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 09:59
 */

namespace App\Enums;


class QueueNamesEnum
{
    const ASYNC_QUEUE_NAME = 'stores_async';
    const SYNC_QUEUE_NAME = 'stores_sync';

    /**
     * @return string[]
     */
    static public function getAll(): array
    {
        return [
            self::SYNC_QUEUE_NAME,
            self::ASYNC_QUEUE_NAME
        ];
    }
}