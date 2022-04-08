<?php

namespace App\Services\Presenters;

use App\Enums\TransactionsExternalStatusesEnum;
use App\Enums\TransactionsInternalStatusesEnum;
use App\Services\Transactions\TransactionImage;

abstract class Presenter
{
    protected $model;

    public function setModel(TransactionImage $image): void
    {
        $this->model = $image;
    }

    abstract public function getValues(): array;

    abstract public function getColumnNames(): array;

    /**
     * Return presenter transactions statuses of registry.
     *
     * @return array
     */
    public static function getTransactionsStatuses(): array
    {
        return auth()->user()->isAdmin()
            ? TransactionsInternalStatusesEnum::all()
            : TransactionsExternalStatusesEnum::all();
    }
}
