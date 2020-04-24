<?php


namespace App\Enums;


interface EnumInterface
{
    public static function getValues(): array;

    public static function getKeys(): array;
}