<?php

namespace App\Services\Presenters;

/**
 * Interface of classes for sorting tables.
 */
interface ViewSortable
{
    /**
     * Return list of column names that can be sorted by user.
     *
     * @return array
     */
    public function getSortColumnKeyNames(): array;
}
