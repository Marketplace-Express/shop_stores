<?php


namespace App\Enums;


abstract class AbstractEnum
{
    public static function getKeys(): array
    {
        return array_keys(
            (new \ReflectionClass(static::class))->getConstants()
        );
    }

    abstract public static function getValues(): array;
}