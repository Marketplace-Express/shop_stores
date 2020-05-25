<?php
/**
 * User: Wajdi Jurry
 * Date: 17 Apr 2020
 * Time: 02:06 PM
 */

namespace App\Exception;


class ValidationFailed extends \Exception
{
    /** @var array */
    public $errors = [];

    public function __construct($errors = [], $code = 400, \Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct(null, $code, $previous);
    }
}