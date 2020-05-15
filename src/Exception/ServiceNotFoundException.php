<?php
/**
 * User: Wajdi Jurry
 * Date: ١٥‏/٥‏/٢٠٢٠
 * Time: ١:٣٣ ص
 */

namespace App\Exception;



class ServiceNotFoundException extends \Exception
{
    public function __construct($message = '', $code = 503, \Throwable $previous = null)
    {
        parent::__construct(sprintf('Unavailable Service "%s"', $message), $code, $previous);
    }
}