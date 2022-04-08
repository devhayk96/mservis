<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotFoundController;
use App\Http\Controllers\TransactionExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require_once 'web/auth.php';

Route::middleware('auth')->group(function () {
    require_once 'web/transactions-registry.php';

    Route::post('transactions/registry-export', [TransactionExportController::class, 'registryExport'])
        ->name('transactions.registry-export');

    Route::post('transactions/export', [TransactionExportController::class, 'export'])
        ->name('transactions.export');

    Route::middleware('is.admin')->group(function () {
        require_once 'web/transactions-sync.php';
        require_once 'web/transactions-processing.php';
        require_once 'web/admin-finance-module.php';
    });

    Route::middleware('is.merchant')->group(function () {
        require_once 'web/merchant-finance-module.php';
    });
});

Route::fallback([NotFoundController::class, 'notFoundForWeb']);
