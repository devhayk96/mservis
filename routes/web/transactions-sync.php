<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionExportController;
use App\Http\Controllers\TransactionImportController;

Route::get('/transactions-sync', function () {
    return view('transactions-sync');
})->name('transactions.sync');

Route::post('transactions-sync/check-file', [TransactionImportController::class, 'checkFile'])
    ->name('transactions.check-file');

Route::post('transactions-sync/import', [TransactionImportController::class, 'import'])
    ->name('transactions.import');
