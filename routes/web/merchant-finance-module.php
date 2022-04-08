<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MerchantFinanceModuleController;

Route::get('merchant-finance-module', [MerchantFinanceModuleController::class, 'index'])
    ->name('merchant-finance-module.index');

Route::post('merchant-finance-module/date-filter', [MerchantFinanceModuleController::class, 'filter'])
    ->name('merchant-finance-module.filter');
