<?php


namespace App\Controller\Validator;


use Symfony\Component\Validator\Constraints\Collection;

interface InputConstraints
{
    public function getConstraints(): Collection;
}