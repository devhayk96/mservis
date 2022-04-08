<?php

namespace App\Enums;

/**
 * Relation between column names and their indexes.
 */
class ImportBanksColumnsEnum
{
    public const COLUMN_BIN = 0;
    public const COLUMN_BANK = 1;
    public const COLUMN_SYSTEM = 2;
    public const COLUMN_TYPE = 3;
    public const COLUMN_LEVEL = 4;
    public const COLUMN_GEO = 5;

    /**
     * File columns.
     */
    public const COLUMNS = [
        self::COLUMN_BIN => 'A',
        self::COLUMN_BANK => 'B',
        self::COLUMN_SYSTEM => 'C',
        self::COLUMN_TYPE => 'D',
        self::COLUMN_LEVEL => 'E',
        self::COLUMN_GEO => 'F',
    ];
}
