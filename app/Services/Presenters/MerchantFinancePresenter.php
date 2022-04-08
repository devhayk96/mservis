<?php

namespace App\Services\Presenters;

use App\Models\Transaction;

class MerchantFinancePresenter
{
    /**
     * Transaction.
     *
     * @var Transaction
     */
    protected $model;

    /**
     * Set model of presenter.
     *
     * @param Transaction $model
     */
    public function setModel(Transaction $model): void
    {
        $this->model = $model;
    }

    /**
     * Return values.
     *
     * @return array
     */
    public function getValues(): array
    {
        $commission = $this->model->commission ? $this->model->commission->amount : 0;
        return [
            $this->model->id,
            $this->model->external_id,
            $this->model->date,
            $this->model->amount,
            $commission ?: '',
            bcadd($this->model->amount, $commission, 2)
        ];
    }

    /**
     * Return column names.
     *
     * @return array
     */
    public function getColumnNames(): array
    {
        return [
            'Internal Id',
            'Transaction Id',
            'Date',
            'Sum',
            'Commission',
            'Sum + commission',
        ];
    }
}