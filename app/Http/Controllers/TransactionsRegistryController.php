<?php

namespace App\Http\Controllers;

use App\Enums\ProcessingOperatorsEnum;
use App\Models\Transaction;
use App\Scopes\TransactionsRegistryAdminScope;
use App\Scopes\TransactionsRegistryMerchantScope;
use App\Scopes\TransactionsRegistryScope;
use App\Services\Presenters\AdminPresenter;
use App\Services\Presenters\MerchantPresenter;
use App\Services\Transactions\TransactionImageFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use App\Enums\TransactionsInternalStatusesEnum;
use App\Services\Presenters\Presenter;
use App\Services\RegistryTotalsService;

/**
 * Transactions registry controller.
 */
class TransactionsRegistryController extends Controller
{
    /**
     * Amount of items on a page.
     */
    protected const ITEMS_PER_PAGE = 50;

    /**
     * Registry index handler.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $sortDirection = $request->direction ?? 'desc';
        $sortColumn = $request->column ?? 'date';
        $presenter = $this->getPresenter();
        $presenterScope = $this->getPresenterScope();

        $transactionsPaginator = Transaction::query()
            ->withGlobalScope($presenterScope, new $presenterScope($request))
            ->with('processing')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(self::ITEMS_PER_PAGE)
            ->withQueryString();

        $transactions = $transactionsPaginator->map(function (Transaction $transaction) use ($presenter) {
            $image = TransactionImageFactory::createFromModel($transaction);
            $presenter->setModel($image);

            return $presenter->getValues();
        });

        if ($request->has('manager') && $request->get('manager') == '') {
            $transactionsPaginator->appends(['manager' => '']);
        }

        return view('transactions-registry', [
            'direction' => $sortDirection,
            'column' => $sortColumn,
            'paginator' => $transactionsPaginator,
            'registryColumnNames' => $presenter->getColumnNames(),
            'registrySortColumnNames' => $presenter->getSortColumnKeyNames(),
            'transactions' => $transactions,
            'operators' => $this->getOperatorsForSelect(),
            'statuses' => $this->getStatusesForSelect(),
            'managers' => $this->getManagerNamesForSelect(),
        ]);
    }

    /**
     * Handler of a registry filter form.
     *
     * @param  Request $request
     *
     * @return Redirector
     */
    public function prepareQuery(Request $request): Redirector
    {
        return redirect(route('transactions-registry.index'));
    }

    /**
     * Totals request handler.
     *
     * @return View
     */
    public function totals(Request $request, RegistryTotalsService $service): View
    {
        $service->setRequest($request);
        $service->setScope($this->getPresenterScope());
        $service->prepareData();

        return view('partials/transactions-registry-totals', [
            'transactionTotalsByStatus' => $service->getTransactionTotalsByStatus(),
            'transactionsCount' => $service->countTransactions(),
            'transactionsSum' => $service->getTransactionAmountsSumFormatted(),
        ]);
    }

    /**
     * Return processing operators for transactions.
     *
     * @return array
     */
    protected function getOperatorsForSelect(): array
    {
        return array_merge([0 => 'Select API'], ProcessingOperatorsEnum::NAMES);
    }

    /**
     * Return statuses for filter select.
     *
     * @return array
     */
    protected function getStatusesForSelect(): array
    {
        $statuses = Presenter::getTransactionsStatuses();
        $statuses[0] = 'all';
        return $statuses;
    }


    /**
     * Return manager names for filter select.
     *
     * @return array
     */
    protected function getManagerNamesForSelect(): array
    {
        return Transaction::groupBy('manager_name')->pluck('manager_name')->toArray();
    }

    /**
     * Return presenter of registry.
     *
     * @return Presenter
     */
    protected function getPresenter(): Presenter
    {
        return auth()->user()->isAdmin()
            ? new AdminPresenter()
            : new MerchantPresenter();
    }

    /**
     * Return presenter scope of registry.
     *
     * @return string
     */
    protected function getPresenterScope(): string
    {
        return auth()->user()->isAdmin()
            ? TransactionsRegistryAdminScope::class
            : TransactionsRegistryMerchantScope::class;
    }

}
