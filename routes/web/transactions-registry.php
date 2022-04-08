<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionsRegistryController;

Route::get('/', [TransactionsRegistryController::class, 'index'])
    ->name('transactions-registry.index');

Route::post('registry/search', [TransactionsRegistryController::class, 'prepareQuery'])
    ->name('transactions-registry.search');

Route::get('registry/totals', [TransactionsRegistryController::class, 'totals'])
    ->name('transactions-registry.totals');
