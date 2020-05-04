<?php
/**
 * User: Wajdi Jurry
 * Date: ٤‏/٥‏/٢٠٢٠
 * Time: ١:٢٨ ص
 */

namespace App\Controller\Validator\Follow;


use App\Controller\Validator\InputConstraints;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Context\ExecutionContext;

class GetFollowedStoresConstraint implements InputConstraints
{

    /**
     * @return Collection
     */
    public function getConstraints(): Collection
    {
        return new Collection([
            'followerId' => [new NotBlank(), new Uuid()],
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