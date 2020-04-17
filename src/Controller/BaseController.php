<?php


namespace App\Controller;


use App\Controller\Validator\InputConstraints;
use App\Exception\ValidationFailed;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validation;

class BaseController extends AbstractController
{
    /**
     * @param array $data
     * @param InputConstraints $constraints
     * @throws ValidationFailed
     */
    protected function validateRequest(array $data, InputConstraints $constraints)
    {
        if (!$constraints instanceof InputConstraints) {
            throw new \InvalidArgumentException(sprintf("%s should implement InputConstraints", get_class($constraints)));
        }

        $validator = Validation::createValidator();
        if (count($errors = $validator->validate($data, $constraints->getConstraints()))) {
            throw new ValidationFailed($errors);
        }
    }

    /**
     * @param $message
     * @param int $code
     * @return array
     */
    public function getErrorResponse($message, int $code): array
    {
        return [
            'status' => $code,
            'message' => $message
        ];
    }
}