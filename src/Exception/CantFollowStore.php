<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 15:45
 */

namespace App\Exception;


use Throwable;

class CantFollowStore extends \Exception
{
    public function __construct($message = "cant follow store", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}