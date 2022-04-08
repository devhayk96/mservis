<?php

namespace App\Services\Presenters;

/**
 * Presenter of a transaction registy (for admins).
 */
class AdminPresenter extends Presenter implements ViewSortable
{
    public function getValues(): array
    {
        return [
            'id'            => $this->model->getId(),
            'date'          => $this->model->getDateFormatted('d.m.Y H:i:s'),
            'card_number'   => $this->model->getCardNumber(),
            'amount'        => $this->model->getFormattedAmount(),
            'external_id'   => $this->model->getExternalId(),
            'manager   '    => $this->model->getManagerName(),
            'processing'    => $this->model->getProcessingOperatorName(),
            'status'        => $this->getStatusForAdmin(),
            'description'   => $this->model->getStatusDescription(),
            'created_at'    => $this->model->getFormattedCreatedAt(),
            'bank'          => $this->model->getBankName()
        ];
    }

    public function getColumnNames(): array
    {
        return [
            'id'            => 'Select value',
            'date'          => 'Date and time',
            'card_number'   => 'Card number',
            'amount'        => 'Summ',
            'external_id'   => 'Transaction id',
            'manager'       => 'Manager',
            'processing'    => 'Executor',
            'status'        => 'Status',
            'description'   => 'Description',
            'created_at'    => 'Transaction Created at',
            'bank'          => 'Bank',
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
            'created_at',
        ];
    }

    /**
     * Return status for admin.
     *
     * @return string
     */
    protected function getStatusForAdmin(): string
    {
        return $this->model->isProcessing()
            ? 'Send supplier'
            : $this->model->getStatus();
    }
}
