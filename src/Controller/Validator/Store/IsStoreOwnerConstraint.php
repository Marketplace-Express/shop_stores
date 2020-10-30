<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/29
 * Time: 11:03
 */

namespace App\Controller\Validator\Store;


use App\Controller\Validator\InputConstraints;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class IsStoreOwnerConstraint extends CommonDataValidator implements InputConstraints
{

    /**
     * @return Collection
     */
    public function getConstraints(): Collection
    {
        return new Collection([
            'user_id' => [new NotBlank(), new Uuid()],
            'storeId' => [new NotBlank(), new Uuid()]
        ]);
    }
}