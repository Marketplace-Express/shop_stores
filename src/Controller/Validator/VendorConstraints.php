<?php


namespace App\Controller\Validator;


use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class VendorConstraints implements InputConstraints
{
    /**
     * @return Collection
     */
    public function getConstraints(): Collection
    {
        return new Collection([
            'ownerId' => [new Uuid(), new NotBlank()]
        ]);
    }
}