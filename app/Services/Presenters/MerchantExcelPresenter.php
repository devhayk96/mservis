<?php

namespace App\Services\Presenters;

/**
 * Presenter for a merchant excel file.
 */
class MerchantExcelPresenter extends Presenter implements ExcelExportable
{
    use HasColumnLetterIndexes;

    public const COLUMN_DATE = 0;
    public const COLUMN_TRANSACTION_ID = 1;
    public const COLUMN_CARD_NUMBER = 2;
    public const COLUMN_AMOUNT = 3;
    public const COLUMN_STATUS = 4;

    public function getValues(): array
    {
        return [
            self::COLUMN_DATE           => $this->model->getDateFormatted('d.m.Y H:i:s'),
            self::COLUMN_TRANSACTION_ID => $this->model->getExternalId(),
            self::COLUMN_CARD_NUMBER    => $this->model->getCardNumberFormatted(),
            self::COLUMN_AMOUNT         => $this->model->getAmount(),
            self::COLUMN_STATUS         => $this->model->getStatus(),
        ];
    }

    public function getColumnNames(): array
    {
        return [
            self::COLUMN_DATE           => 'Date and time',
            self::COLUMN_TRANSACTION_ID => 'Transaction id',
            self::COLUMN_CARD_NUMBER    => 'Card number',
            self::COLUMN_AMOUNT         => 'Summ',
            self::COLUMN_STATUS         => 'Status',
        ];
    }

    /**
     * Return list of column names that must be strings.
     *
     * @return array
     */
    public function getColumnsOfExplicitStringType(): array
    {
        return [
            $this->getLetterIndexByIntIndex(self::COLUMN_CARD_NUMBER),
        ];
    }
}
