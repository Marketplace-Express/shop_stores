<?php
/**
 * User: Wajdi Jurry
 * Date: 19/10/18
 * Time: 04:49 م
 */

namespace App\Exception;


class OperationFailed extends BaseException
{
    /**
     * OperationFailed constructor.
     * @param $messages
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($messages, int $code = 503, \Throwable $previous = null)
    {
        parent::__construct($messages, $code, $previous);
    }
}
