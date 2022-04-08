<?php

namespace App\Services\Presenters;

/**
 * Presenter for an admin excel file.
 */
class AdminExcelPresenter extends Presenter implements ExcelExportable
{
    use HasColumnLetterIndexes;

    public const COLUMN_ID = 0;
    public const COLUMN_DATE = 1;
    public const COLUMN_CARD_NUMBER = 2;
    public const COLUMN_AMOUNT = 3;
    public const COLUMN_TRANSACTION_ID = 4;
    public const COLUMN_MANAGER_NAME = 5;
    public const COLUMN_STATUS = 6;
    public const COLUMN_COMMENT = 7;
    public const COLUMN_CREATED_AT = 8;
    public const COLUMN_EXECUTION_DATE = 9;
    public const COLUMN_BANK = 10;
    public const COLUMN_MERCHANT = 11;
    public const COLUMN_CURRENCY = 12;
    public const COLUMN_PROCESSING_OPERATOR = 13;
    public const COLUMN_STATUS_DESCRIPTION = 14;

    public function getValues(): array
    {
        return [
            self::COLUMN_ID                  => $this->model->getId(),
            self::COLUMN_DATE                => $this->model->getDateFormatted('d.m.Y H:i:s'),
            self::COLUMN_CARD_NUMBER         => $this->model->getCardNumber(),
            self::COLUMN_AMOUNT              => $this->model->getAmount(),
            self::COLUMN_TRANSACTION_ID      => $this->model->getExternalId(),
            self::COLUMN_MANAGER_NAME        => $this->model->getManagerName(),
            self::COLUMN_STATUS              => $this->model->getStatusForAdmin(),
            self::COLUMN_COMMENT             => $this->model->getComment(),
            self::COLUMN_CREATED_AT          => $this->model->getFormattedCreatedAt(),
            self::COLUMN_EXECUTION_DATE      => $this->model->getExecutionDateFormatted(),
            self::COLUMN_BANK                => $this->model->getBankName(),
            self::COLUMN_MERCHANT            => (string) optional($this->model->getMerchant())->name,
            self::COLUMN_CURRENCY            => $this->model->getCurrency(),
            self::COLUMN_PROCESSING_OPERATOR => $this->model->getProcessingOperatorName(),
            self::COLUMN_STATUS_DESCRIPTION  => $this->model->getStatusDescription(),
        ];
    }

    public function getColumnNames(): array
    {
        return [
            self::COLUMN_ID                  => 'Internal ID',
            self::COLUMN_DATE                => 'Date',
            self::COLUMN_CARD_NUMBER         => 'Card number',
            self::COLUMN_AMOUNT              => 'Summ',
            self::COLUMN_TRANSACTION_ID      => 'Transaction id',
            self::COLUMN_MANAGER_NAME        => 'Manager',
            self::COLUMN_STATUS              => 'Status',
            self::COLUMN_COMMENT             => 'Comment',
            self::COLUMN_CREATED_AT          => 'Transaction created at',
            self::COLUMN_EXECUTION_DATE      => 'Execution date',
            self::COLUMN_BANK                => 'Bank',
            self::COLUMN_MERCHANT            => 'Merchant',
            self::COLUMN_CURRENCY            => 'Currency',
            self::COLUMN_PROCESSING_OPERATOR => 'Executor',
            self::COLUMN_STATUS_DESCRIPTION  => 'Description',
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
            $this->getLetterIndexByIntIndex(self::COLUMN_TRANSACTION_ID)
        ];
    }
}
