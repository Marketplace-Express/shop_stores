<?php


namespace App\Enums;


class StoreType implements EnumInterface
{
    const TYPE_PHYSICAL = 0;
    const TYPE_VIRTUAL = 1;

    public static function getValues(): array
    {
        return [
            self::TYPE_PHYSICAL,
            self::TYPE_VIRTUAL
        ];
    }

    public static function getKeys(): array
    {
        return array_keys(
            (new \ReflectionClass(self::class))->getConstants()
        );
    }
}