<?php

namespace App\Enums;

/**
 * Transaction statuses.
 */
abstract class BaseTransactionsStatusesEnum
{
    /**
     * Statuses.
     */
    public const STATUSES = [];

    /**
     * Return all statuses.
     *
     * @return array
     */
    public static function all(): array
    {
        return static::STATUSES;
    }

    /**
     * Check status existence by its name.
     *
     * @param  string $statusName
     *
     * @return bool
     */
    public static function isKnownStatus(string $statusName): bool
    {
        return ((int) array_search($statusName, static::STATUSES)) > 0;
    }

    /**
     * Check status existence by its ID.
     *
     * @param  int $statusId
     *
     * @return bool
     */
    public static function isKnownStatusId(int $statusId): bool
    {
        return ((int) array_search($statusId, array_keys(static::STATUSES))) > 0;
    }

    /**
     * Return status name by a given status ID.
     *
     * @param  int $statusId
     *
     * @return string
     */
    public static function getNameById(int $statusId): string
    {
        return data_get(static::STATUSES, $statusId, static::STATUSES[0]);
    }
}
