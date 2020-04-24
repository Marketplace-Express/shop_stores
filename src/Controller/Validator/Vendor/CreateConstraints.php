<?php


namespace App\Controller\Validator\Vendor;


use App\Controller\Validator\InputConstraints;
use App\Enums\StoreType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Context\ExecutionContext;

class CreateConstraints implements InputConstraints
{
    /**
     * @return Collection
     */
    public function getConstraints(): Collection
    {
        return new Collection([
            'ownerId' => [new Uuid(), new NotBlank()],
            'name' => [new NotBlank(), new Length(['max' => 100])],
            'description' => [new Length(['allowEmptyString' => true, 'min' => 1, 'normalizer' => function($text) {
                $text = trim($text);
                $text = strip_tags($text);
                return $text;
            }])],
            'type' => [new Choice(['choices' => StoreType::getValues()])],
            'location' => [new Callback(['callback' => function ($location, ExecutionContext $context) {
                if (!empty($location) && is_array($location)) {
                    if (!array_key_exists('coordinates', $location)) {
                        $context->addViolation('Invalid location');
                        return false;
                    }
                }
                return true;
            }])],
            'photo' => [new Url()],
            'coverPhoto' => [new Url()]
        ]);
    }
}