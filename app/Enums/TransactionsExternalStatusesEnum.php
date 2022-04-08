<?php

namespace App\Enums;

/**
 * Transaction external statuses.
 */
class TransactionsExternalStatusesEnum extends BaseTransactionsStatusesEnum
{
    public const STATUS_UNKNOWN = 0;
    public const STATUS_PENDING = 1;
    public const STATUS_SUCCESS = 2;
    public const STATUS_FAIL    = 3;

    /**
     * Statuses.
     */
    public const STATUSES = [
        self::STATUS_UNKNOWN => 'unknown',
        self::STATUS_PENDING => 'pending',
        self::STATUS_SUCCESS => 'success',
        self::STATUS_FAIL    => 'fail',
    ];
}
