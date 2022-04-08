<?php

namespace App\Services;

use App\Enums\TransactionsInternalStatusesEnum;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Services\Presenters\MerchantFinancePresenter;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

/**
 * Merchant finance page service.
 */
class MerchantFinanceService
{
    /**
     * Amount of items on a page.
     */
    protected const ITEMS_PER_PAGE = 25;

    /**
     * Merchant
     *
     * @var Merchant
     */
    protected $merchant;

    /**
     * Transactions for a certain page.
     *
     * @var LengthAwarePaginator
     */
    protected $paginator;

    /**
     * Date from.
     *
     * @var Carbon|null
     */
    protected $dateFrom;

    /**
     * Date to.
     *
     * @var Carbon|null
     */
    protected $dateTo;

    /**
     * Set dates from request.
     *
     * @param Request $request
     */
    public function setDatesFromRequest(Request $request): void
    {
        $this->dateFrom = $request->date_from
            ? Carbon::parse($request->date_from)
            : now()->startOfDay();

        $this->dateTo = $request->date_to
            ? Carbon::parse($request->date_to)->endOfDay()
            : now()->endOfDay();
    }

    /**
     * Set merchant.
     *
     * @param Merchant $merchant
     */
    public function setMerchant(Merchant $merchant): void
    {
        $this->merchant = $merchant;
    }

    /**
     * Return merchant.
     *
     * @return Merchant
     */
    public function getMerchant(): Merchant
    {
        return $this->merchant;
    }

    /**
     * Prepare data.
     *
     * @return void
     */
    public function prepareData(): void
    {
        $this->paginator = Transaction::query()
            ->where('status', TransactionsInternalStatusesEnum::STATUS_SUCCESS)
            ->betweenDates($this->dateFrom, $this->dateTo)
            ->with('commission.dailyCommission')
            ->paginate(self::ITEMS_PER_PAGE)
            ->withQueryString();

        $this->transactionAggregate = Transaction::query()
            ->selectRaw('sum(transactions.amount) as sum, sum(transaction_commissions.amount) as commission')
            ->leftJoin('transaction_commissions', 'transactions.id', '=', 'transaction_commissions.transaction_id')
            ->where('status', TransactionsInternalStatusesEnum::STATUS_SUCCESS)
            ->betweenDates($this->dateFrom, $this->dateTo)
            ->groupBy('merchant_id')
            ->first();
    }

    /**
     * Return total amount of all success transactons.
     *
     * @return float
     */
    public function getTransactionsTotalAmount(): float
    {
        return optional($this->transactionAggregate)->sum ?: 0;
    }

    /**
     * Return total amount of all commissions.
     *
     * @return float
     */
    public function getTransactionsTotalCommissions(): float
    {
        return optional($this->transactionAggregate)->commission ?: 0;
    }

    /**
     * Return total amount of all commissions.
     *
     * @return float
     */
    public function getTransactionsTotalAmountAndCommissions(): float
    {
        return (float) bcadd($this->getTransactionsTotalAmount(), $this->getTransactionsTotalCommissions(), 2);
    }

    /**
     * Return column names of a table.
     *
     * @return array
     */
    public function getTableColumnNames(): array
    {
        return (new MerchantFinancePresenter())->getColumnNames();
    }

    /**
     * Return transactions data.
     *
     * @return Collection
     */
    public function getTransactions(): Collection
    {
        return $this->paginator->map(function ($item) {
            $presenter = new MerchantFinancePresenter();
            $presenter->setModel($item);
            return $presenter->getValues();
        });
    }

    /**
     * Return paginator.
     *
     * @return LengthAwarePaginator
     */
    public function getPaginator(): LengthAwarePaginator
    {
        return $this->paginator;
    }
}
