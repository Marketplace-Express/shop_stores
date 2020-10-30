<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 11:21
 */

namespace App\Controller\Validator\Follow;


use App\Controller\Validator\InputConstraints;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class RemoveStoreFollowersConstraint implements InputConstraints
{

    /**
     * @return Collection
     */
    public function getConstraints(): Collection
    {
        return new Collection([
            'storeId' => [new NotBlank(), new Uuid()]
        ]);
    }
}