<?php


namespace App\Exception;


use Throwable;

class NotFound extends \Exception
{
    public function __construct($message = "Entity not found", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}