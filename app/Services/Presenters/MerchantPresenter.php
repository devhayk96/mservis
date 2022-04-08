<?php

namespace App\Services\Presenters;

/**
 * Presenter of a transaction registy (for merchants).
 */
class MerchantPresenter extends Presenter implements ViewSortable
{
    public function getValues(): array
    {
        return [
            'date'          => $this->model->getDateFormatted('d.m.Y H:i:s'),
            'card_number'   => $this->model->getCardNumberFormatted(),
            'amount'        => $this->model->getFormattedAmount(),
            'external_id'   => $this->model->getExternalId(),
            'status'        => $this->model->getStatus(),
        ];
    }

    public function getColumnNames(): array
    {
        return [
            'date'          => 'Date and time',
            'card_number'   => 'Card number',
            'amount'        => 'Summ',
            'id'            => 'Transaction id',
            'status'        => 'Status',
        ];
    }

    public function getSortColumnKeyNames(): array
    {
        return [
            'date',
            'card_number',
            'amount',
            'id',
            'status',
        ];
    }
}
