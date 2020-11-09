<?php
/**
 * User: Wajdi Jurry
 * Date: ٢‏/٥‏/٢٠٢٠
 * Time: ٢:٣٥ ص
 */

namespace App\Controller;


use App\Controller\Validator\InputConstraints;
use App\Exception\ValidationFailed;
use App\Entity\Interfaces\ApiArrayData;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
     * @param $content
     * @param int $code
     * @return JsonResponse
     */
    public function prepareResponse($content, int $code = Response::HTTP_OK): JsonResponse
    {
        $response = null; // initialize response variable

        if ($content instanceof ApiArrayData) {
            $response = $content->toApiArray();
        }

        if (is_array($content) || $content instanceof Collection) {
            array_walk_recursive($content, function (&$value) {
                $value = ($value instanceof ApiArrayData) ? $value->toApiArray() : $value;
            });
        }

        if ($_ENV['APP_ENV'] !== 'dev' && $code == Response::HTTP_INTERNAL_SERVER_ERROR) {
            $response = 'internal server error';
        }

        return $this->json([
            'status' => $code,
            'message' => $response ?? $content
        ], $code);
    }
}