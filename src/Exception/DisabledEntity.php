<?php
/**
 * User: Wajdi Jurry
 * Date: ١‏/٥‏/٢٠٢٠
 * Time: ١:١٧ ص
 */

namespace App\Exception;


use Throwable;

class DisabledEntity extends \Exception
{
    public function __construct($message = "Disabled entity", $code = 422, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}