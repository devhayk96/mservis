<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminFinanceModuleController;

Route::get('admin-finance-module', [AdminFinanceModuleController::class, 'index'])
    ->name('admin-finance-module.index');

Route::post('admin-finance-module/set-merchant-balance', [AdminFinanceModuleController::class, 'setBalance'])
    ->name('admin-finance-module.set-merchant-balance');

Route::post('admin-finance-module/set-commission', [AdminFinanceModuleController::class, 'setCommission'])
    ->name('admin-finance-module.set-commission');
