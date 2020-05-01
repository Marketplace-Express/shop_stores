<?php


namespace App\Controller\Validator\Store;


use App\Controller\Validator\InputConstraints;
use App\Enums\DisableReasonEnum;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Context\ExecutionContext;

class DisableConstraint extends CommonDataValidator implements InputConstraints
{

    /**
     * @return Collection
     */
    public function getConstraints(): Collection
    {
        return new Collection([
            'storeId' => [new NotBlank(), new Uuid()],
            'disableReason' => [new NotBlank(), new Choice(['choices' => DisableReasonEnum::getValues(), 'max' => 1])],
            'disableComment' => [new Callback(['callback' => function($text, ExecutionContext $context) {
                return $this->validateText($text, $context, 'disableComment', ['<b><i>']);
            }])]
        ]);
    }
}