<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Http\Request;

class TransactionsRegistryMerchantScope extends BaseTransactionsRegistryScope implements Scope
{
    /**
     * Constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->when($this->status, function (Builder $query) {
                $query->where('status', $this->status);
            })
            ->when($this->dateFrom, function (Builder $query) {
                $query->where('date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function (Builder $query) {
                $query->where('date', '<=', $this->dateTo->endOfDay());
            })
            ->when($this->amountFrom, function (Builder $query) {
                $query->where('amount', '>=', $this->amountFrom);
            })
            ->when($this->amountTo, function (Builder $query) {
                $query->where('amount', '<=', $this->amountTo);
            })
            ->when($this->search, function (Builder $query) {
                $query
                    ->where('card_number', $this->search)
                    ->orWhere('external_id', $this->search);
            })
            ->when($this->column, function (Builder $query) {
                $query->orderBy($this->column, $this->direction);
            });
    }

}
