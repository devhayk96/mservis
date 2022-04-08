<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExportExcel as TransactionsExport;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Transactions export controller.
 */
class TransactionExportController extends Controller
{
    /**
     * Download a file with transactions.
     *
     * @return TransactionsExport
     */
    public function export(Request $request): TransactionsExport
    {
        $format = 'Y-m-d H:i:s';

        $dateFrom = ($request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subDays(30))->format($format);
        $dateTo = ($request->date_to ? Carbon::parse($request->date_to) : Carbon::now())->endOfDay()->format($format);

        $request->merge([
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        return new TransactionsExport($request->all());
    }

    /**
     * Download a file with transactions.
     *
     * For a transactions registry.
     *
     * @return TransactionsExport
     */
    public function registryExport(Request $request): TransactionsExport
    {
        return new TransactionsExport($request->all());
    }
}
