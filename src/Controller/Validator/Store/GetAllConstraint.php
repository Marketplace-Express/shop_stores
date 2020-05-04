<?php
/**
 * User: Wajdi Jurry
 * Date: ١‏/٥‏/٢٠٢٠
 * Time: ١:٣٤ ص
 */

namespace App\Controller\Validator\Store;


use App\Controller\Validator\InputConstraints;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Context\ExecutionContext;

class GetAllConstraint implements InputConstraints
{

    /**
     * @return Collection
     */
    public function getConstraints(): Collection
    {
        return new Collection([
            'sort' => [new Callback(['callback' => function ($sort, ExecutionContext $context) {
                if (false == json_decode($sort)) {
                    $context->addViolation('Invalid sorting params');
                    return false;
                }
                return true;
            }])],
            'page' => [new Callback(['callback' => [$this, "validatePageAndLimit"]]), new Range(['min' => 1])],
            'limit' => [new Callback(['callback' => [$this, "validatePageAndLimit"]]), new Range(['min' => 1, 'max' => 100])]
        ]);
    }

    /**
     * @param $value
     * @param ExecutionContext $context
     * @return bool
     */
    public function validatePageAndLimit($value, ExecutionContext $context): bool
    {
        if (!is_numeric($value)) {
            $context->addViolation('Page and Limit should be valid integers');
            return false;
        }

        return true;
    }
}