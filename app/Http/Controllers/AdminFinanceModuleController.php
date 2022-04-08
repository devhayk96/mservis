<?php

namespace App\Http\Controllers;

use App\Models\DailyCommission;
use App\Models\Merchant;
use App\Models\MerchantBalanceWriteUp;
use App\Services\DailyCommissionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Admin finance module controller.
 */
class AdminFinanceModuleController extends Controller
{
    /**
     * Module index handler.
     *
     * @param  Request                $request
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $merchants = Merchant::all();
        return view('admin-finance-module', compact('merchants'));
    }

    /**
     * Balance form handler.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function setBalance(Request $request): RedirectResponse
    {
        $request->validateWithBag('balance', [
            'merchant_id' => 'required|exists:merchants,id',
            'date' => 'required|date|date_format:d.m.Y',
            'rate' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
            'sum' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ], [], [
            'merchant_id' => 'merchant',
        ]);

        $writeUp = MerchantBalanceWriteUp::create([
            'merchant_id' => $request->merchant_id,
            'date' => Carbon::parse($request->date)->startOfDay(),
            'comment' => (string) $request->comment,
            'rate' => $request->rate,
            'commission' => $request->commission,
            'sum' => $request->sum,
            'total_amount' => $request->total_amount,
        ]);

        $merchant = Merchant::find($request->merchant_id);
        $merchant->balance = bcadd($merchant->balance, $writeUp->total_amount, 2);
        $merchant->save();

        return redirect(route('admin-finance-module.index'));
    }

    /**
     * Commission form handler
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function setCommission(Request $request, DailyCommissionService $commissionService): RedirectResponse
    {
        $request->validateWithBag('commission', [
            'amount' => 'required|integer|min:0',
            'date' => 'required|date|date_format:d.m.Y',
            'merchant_id' => 'required|exists:merchants,id',
        ], [], [
            'merchant_id' => 'merchant',
        ]);

        $commissionService->setCommissionForMerchant(
            $request->merchant_id,
            Carbon::parse($request->date)->startOfDay(),
            (float) $request->amount
        );

        return redirect(route('admin-finance-module.index'));
    }
}
