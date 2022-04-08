<?php

namespace App\Enums;

/**
 * Permissions names
 */
class PermissionsEnum
{
    public const TRANSACTION_LIST = 'TRANSACTION_LIST';
    public const TRANSACTION_IMPORT = 'TRANSACTION_IMPORT';
    public const TRANSACTION_EXPORT = 'TRANSACTION_EXPORT';
    public const TRANSACTION_EXPORT_IMPORT = 'TRANSACTION_EXPORT_IMPORT';
    public const TRANSACTION_FILTER = 'TRANSACTION_FILTER';
    public const TRANSACTION_SORTING = 'TRANSACTION_SORTING';
    public const TRANSACTION_DATE_COLUMN = 'TRANSACTION_DATE_COLUMN';
    public const TRANSACTION_MERCHANT_COLUMN = 'TRANSACTION_MERCHANT_COLUMN';
    public const TRANSACTION_CURRENCY_COLUMN = 'TRANSACTION_CURRENCY_COLUMN';
    public const TRANSACTION_MANAGER_COLUMN = 'TRANSACTION_MANAGER_COLUMN';
    public const TRANSACTION_COMMENT_COLUMN = 'TRANSACTION_COMMENT_COLUMN';
    public const TRANSACTION_EXECUTION_DATE_COLUMN = 'TRANSACTION_EXECUTION_DATE_COLUMN';
    public const TRANSACTION_EXECUTOR_COLUMN = 'TRANSACTION_EXECUTOR_COLUMN';
    public const TRANSACTION_BANK_COLUMN = 'TRANSACTION_BANK_COLUMN';

    /**
     * Names.
     */
    public const NAMES = [
        self::TRANSACTION_LIST => 'transaction-list',
        self::TRANSACTION_IMPORT => 'transaction-import',
        self::TRANSACTION_EXPORT => 'transaction-export',
        self::TRANSACTION_EXPORT_IMPORT => 'transaction-export-import',
        self::TRANSACTION_FILTER => 'transaction-filter',
        self::TRANSACTION_SORTING => 'transaction-sorting',
        self::TRANSACTION_DATE_COLUMN => 'transaction-date-column',
        self::TRANSACTION_MERCHANT_COLUMN => 'transaction-merchant-column',
        self::TRANSACTION_CURRENCY_COLUMN => 'transaction-currency-column',
        self::TRANSACTION_MANAGER_COLUMN => 'transaction-manager-column',
        self::TRANSACTION_COMMENT_COLUMN => 'transaction-comment-column',
        self::TRANSACTION_EXECUTION_DATE_COLUMN => 'transaction-execution-date-column',
        self::TRANSACTION_EXECUTOR_COLUMN => 'transaction-executor-column',
        self::TRANSACTION_BANK_COLUMN => 'transaction-bank-column',
    ];

    /**
     * @return string[]
     */
    public static function all(): array
    {
        return self::NAMES;
    }
}
