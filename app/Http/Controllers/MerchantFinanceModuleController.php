<?php

namespace App\Http\Controllers;

use App\Services\DailyCommissionService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Services\MerchantFinanceService;
use Illuminate\Http\RedirectResponse;

/**
 * Merchant finance module controller.
 */
class MerchantFinanceModuleController extends Controller
{
    /**
     * Module index handler.
     *
     * @param  Request                $request
     * @param  DailyCommissionService $commissionService
     * @param  MerchantFinanceService $merchantFinaceService
     *
     * @return View
     */
    public function index(
        Request $request,
        DailyCommissionService $commissionService,
        MerchantFinanceService $merchantFinaceService
    ): View {
        $merchant = auth()->user()->merchant;

        $merchantFinaceService->setDatesFromRequest($request);
        $merchantFinaceService->setMerchant($merchant);
        $merchantFinaceService->prepareData();

        return view('merchant-finance-module', [
            'balance' => $merchant->balance,
            'commission' => $commissionService->getCommissionByMerchantId($merchant->id),
            'totalAmount' => $merchantFinaceService->getTransactionsTotalAmount(),
            'totalCommissions' => $merchantFinaceService->getTransactionsTotalCommissions(),
            'totalAmountAndCommissions' => $merchantFinaceService->getTransactionsTotalAmountAndCommissions(),
            'columnNames' => $merchantFinaceService->getTableColumnNames(),
            'transactions' => $merchantFinaceService->getTransactions(),
            'paginator' => $merchantFinaceService->getPaginator(),
        ]);
    }

    /**
     * Prepare redirect with filtered parameters.
     *
     * @param  Request $request
     *
     * @return RedirectResponse
     */
    public function filter(Request $request): RedirectResponse
    {
        return redirect()->route('merchant-finance-module.index', [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to
        ]);
    }
}
