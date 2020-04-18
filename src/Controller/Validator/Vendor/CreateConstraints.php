<?php


namespace App\Controller\Validator\Vendor;


use App\Controller\Validator\InputConstraints;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class CreateConstraints implements InputConstraints
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