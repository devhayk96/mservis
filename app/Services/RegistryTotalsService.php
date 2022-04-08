<?php

namespace App\Services;

use App\Enums\TransactionsInternalStatusesEnum;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Registry totals service.
 */
class RegistryTotalsService
{
    /**
     * Request for filter.
     *
     * @var Request
     */
    protected $request;

    /**
     * @var $scope
     */
    protected $scope;

    /**
     * Aggregated query result.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $transactionAggregate;

    /**
     * Set request.
     *
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * Prepare data.
     *
     * @return void
     */
    public function prepareData(): void
    {
        $this->transactionAggregate = Transaction::query()
            ->withGlobalScope($this->scope, new $this->scope($this->request))
            ->groupBy('status')
            ->selectRaw('status, sum(amount) as sum, count(id) as count')
            ->get();
    }

    /**
     * Get get transaction totals grouped by statuses.
     *
     * @return Collection
     */
    public function getTransactionTotalsByStatus(): Collection
    {
        return $this->transactionAggregate->pluck('sum', 'status')
            ->mapWithKeys(function ($item, $key) {
                return [TransactionsInternalStatusesEnum::getNameById($key) => $this->formatAmount($item)];
            });
    }

    /**
     * Count transactions.
     *
     * @return int
     */
    public function countTransactions(): int
    {
        return $this->transactionAggregate->reduce(function ($carry, $item) {
            return $carry + $item->count;
        }, 0);
    }

    /**
     * Sum transaction amounts.
     *
     * @return float
     */
    public function sumTransactionAmounts(): float
    {
        return $this->transactionAggregate->reduce(function ($carry, $item) {
            return $carry + $item->sum;
        }, 0);
    }

    /**
     * Return formatted sum of transaction amounts.
     *
     * @return string
     */
    public function getTransactionAmountsSumFormatted(): string
    {
        return $this->formatAmount($this->sumTransactionAmounts());
    }

    /**
     * Format amount.
     *
     * @param  float  $amount
     *
     * @return string
     */
    protected function formatAmount(float $amount): string
    {
        return number_format($amount, 2, ',', ' ');
    }
}
