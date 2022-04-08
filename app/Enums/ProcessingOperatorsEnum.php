<?php

namespace App\Enums;

/**
 * Processing operators enumeration.
 */
class ProcessingOperatorsEnum
{
    /**
     * ID of "Армакс" API (aka Api.Super).
     */
    public const ARMAX = 1;

    /**
     * ID of "Pompay" API.
     */
    public const POMPAY = 2;

    /**
     * Codes of the operators.
     */
    public const CODES = [
        self::ARMAX  => 'armax',
        self::POMPAY  => 'pompay',
    ];

    /**
     * Names of the operators.
     */
    public const NAMES = [
        self::ARMAX  => 'API.Super',
        self::POMPAY  => 'API.Pompay',
    ];

    /**
     * Return code by a given operator ID.
     *
     * @param  int    $operatorId
     *
     * @return string
     */
    public static function getCode(int $operatorId): string
    {
        return data_get(self::CODES, $operatorId, '');
    }

    /**
     * Return name by a given operator ID.
     *
     * @param  int    $operatorId
     *
     * @return string
     */
    public static function getName(int $operatorId): string
    {
        return data_get(self::NAMES, $operatorId, '');
    }

    /**
     * Return names array ready to put in the select tag.
     *
     * @return array
     */
    public static function getNamesForSelect(): array
    {
        return self::NAMES;
    }
}
