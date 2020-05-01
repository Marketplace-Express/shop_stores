<?php


namespace App\Enums;


class DisableReasonEnum extends AbstractEnum
{
    const REASON_INFRACTION_TERMS = 1;
    const REASON_REQUESTED_BY_USER = 2;

    public static function getValues(): array
    {
        return [
            self::REASON_INFRACTION_TERMS,
            self::REASON_REQUESTED_BY_USER
        ];
    }

    public static function getKeys(): array
    {
        return array_keys(
            (new \ReflectionClass(self::class))->getConstants()
        );
    }

    public static function getKey($value)
    {
        $constants = (new \ReflectionClass(self::class))->getConstants();
        return array_search($value, $constants);
    }
}