<?php
/**
 * User: Wajdi Jurry
 * Date: ٢‏/٥‏/٢٠٢٠
 * Time: ٢:٣٩ ص
 */

namespace App\Controller\Validator\Follow;


use App\Controller\Validator\InputConstraints;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class FollowConstraint implements InputConstraints
{

    /**
     * @return Collection
     */
    public function getConstraints(): Collection
    {
        return new Collection([
            'followerId' => [new NotBlank(), new Uuid()],
            'storeId' => [new NotBlank(), new Uuid()]
        ]);
    }
}