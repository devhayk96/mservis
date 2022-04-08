<?php

namespace App\Services\Presenters;

/**
 * Helper for Excel-like column names.
 */
trait HasColumnLetterIndexes
{
    /**
     * Return Excel-like column name by an integer.
     *
     * @param  int    $intIndex
     *
     * @return string
     */
    public function getLetterIndexByIntIndex(int $intIndex): string
    {
        for ($lentterIndex = ""; $intIndex >= 0; $intIndex = intval($intIndex / 26) - 1) {
            $lentterIndex = chr($intIndex % 26 + 0x41) . $lentterIndex;
        }

        return $lentterIndex;
    }
}
