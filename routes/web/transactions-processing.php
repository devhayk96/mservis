<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionProcessingController;

Route::get('transactions-processing/send', [TransactionProcessingController::class, 'send'])
    ->name('transactions-processing.send');
