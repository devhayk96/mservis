<?php

namespace App\Services\Presenters;

/**
 * Interface of classes for excel files.
 */
interface ExcelExportable
{
    /**
     * Return list of column names that must be strings.
     *
     * @return array
     */
    public function getColumnsOfExplicitStringType(): array;
}
