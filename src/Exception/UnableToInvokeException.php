<?php
/**
 * User: Wajdi Jurry
 * Date: ١٥‏/٥‏/٢٠٢٠
 * Time: ١:٣٨ ص
 */

namespace App\Exception;


class UnableToInvokeException extends \Exception
{
    public function __construct($message = "", $code = 405, \Throwable $previous = null)
    {
        parent::__construct(sprintf("Method %s is not invokable", $message), $code, $previous);
    }
}