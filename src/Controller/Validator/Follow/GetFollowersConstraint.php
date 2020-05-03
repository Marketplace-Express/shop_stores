<?php
/**
 * User: Wajdi Jurry
 * Date: ٢‏/٥‏/٢٠٢٠
 * Time: ٥:٣٧ م
 */

namespace App\Controller\Validator\Follow;


use App\Controller\Validator\InputConstraints;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class GetFollowersConstraint implements InputConstraints
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