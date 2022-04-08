<?php

namespace App\Http\Controllers;

use App\Enums\ProcessingOperatorsEnum;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\Processing\PaymentProcessingService;
use Illuminate\Http\RedirectResponse;

/**
 * Transactions processing controller.
 */
class TransactionProcessingController extends Controller
{
    /**
     * Max amount that we can send to an Armax API.
     */
    protected const ARMAX_AMOUNT_LIMITATION = 15000;

    /**
     * Send transaction to processing.
     *
     * @param  Request                  $request
     * @param  PaymentProcessingService $processingService
     *
     * @return Redirector
     */
    public function send(Request $request, PaymentProcessingService $processingService): RedirectResponse
    {
        $request->validate([
            'processingId' => 'required|integer|in:' . implode(',', array_keys(ProcessingOperatorsEnum::NAMES)),
            'transactionsIds' => 'required|array',
        ]);

        $processingOperatorId = $request->get('processingId');
        $transactionsIds = $request->get('transactionsIds');

        $transactions = Transaction::query()
            ->whereIn('id', $transactionsIds)
            ->where('amount', '<=', self::ARMAX_AMOUNT_LIMITATION)
            ->whichNotInProcessing()
            ->get();

        $processingService->processMany($transactions, $processingOperatorId);

        $statusMessage = sprintf('%s transaction(s) have been staged to processing.', $transactions->count());

        return redirect(route('transactions-registry.index'))
            ->with('status', $statusMessage);
    }
}
