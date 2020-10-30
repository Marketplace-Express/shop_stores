<?php


namespace App\Controller\Validator\Store;


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

class CreateConstraints extends CommonDataValidator implements InputConstraints
{
    /**
     * @return Collection
     */
    public function getConstraints(): Collection
    {
        return new Collection([
            'ownerId' => [new NotBlank(), new Uuid()],
            'name' => [
                new NotBlank(),
                new Length(['max' => 100]),
                new Callback(['callback' => function ($name, ExecutionContext $context) {
                    return $this->validateText($name, $context, 'name');
                }])
            ],
            'description' => [new Length(['allowEmptyString' => true, 'min' => 5]), new Callback(['callback' => function ($text, ExecutionContext $context) {
                return $this->validateText($text, $context, 'description');
            }])],
            'type' => [new Choice(['choices' => StoreType::getValues()])],
            'location' => [new Callback(['callback' => function ($location, ExecutionContext $context) {
                return $this->validateLocation($location, $context);
            }])],
            'photo' => [new Url(), new Callback(['callback' => function ($photo, ExecutionContext $context) {
                return $this->validatePhotoAndCoverUrls($photo, $context);
            }])],
            'coverPhoto' => [new Url(), new Callback(['callback' => function ($coverPhotoUrl, ExecutionContext $context) {
                return $this->validatePhotoAndCoverUrls($coverPhotoUrl, $context);
            }])]
        ]);
    }
}