<?php


namespace App\Exception;


use Symfony\Component\Validator\ConstraintViolationListInterface;

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