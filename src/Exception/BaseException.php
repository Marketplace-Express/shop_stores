<?php
/**
 * User: Wajdi Jurry
 * Date: ٢٣‏/٨‏/٢٠١٩
 * Time: ٢:٠٥ ص
 */

namespace App\Exception;


use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Class BaseException
 * @package App\Exceptions
 */
abstract class BaseException extends \Exception
{
    /**
     * BaseException constructor.
     * @param string $messages
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($messages = "", $code = 0, \Throwable $previous = null)
    {
        if (is_array($messages) || is_object($messages)) {
            $errors = [];
            foreach ($messages as $key => $message) {
                if ($message instanceof ConstraintViolationInterface) {
                    $errors[] = $message->getMessage();
                } elseif ($message instanceof \Throwable) {
                    $errors[$key] = $message->getMessage();
                } else {
                    $errors[$key] = $message;
                }
            }
            $errors = json_encode($errors);
        } else {
            $errors = $messages;
        }
        $this->message = $errors;
        $this->code = $code;
        parent::__construct($this->message, $this->code, $previous);
    }
}
