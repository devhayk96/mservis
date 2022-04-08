<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ApiError;
use App\Exceptions\Errors;
use App\Services\Transactions\TransactionByRequest;

class TransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionByRequest $transactionService)
    {
        $request = request();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'token' => 'required'
        ], [
            'token.required' => Errors::CODE_0400003,
            'id.required' => Errors::CODE_0400004
        ]);

        if ($validator->fails()) {
            throw new ApiError($validator->errors()->first());
        }

        $transactionService->setRequest($request);
        $transactions = $transactionService
            ->getTransactions()
            ->map(function (Transaction $transaction) {
                return [
                    'id' => $transaction->external_id,
                    'status' => $transaction->getStatus(),
                    'amount' => $transaction->amount,
                    'internal_id' => $transaction->id,
                    'comment' => $transaction->comment
                ];
            });

        return $this->success($transactions->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, TransactionByRequest $transactionService)
    {
        $transactionService->setRequest($request);
        $transactionService->validateStoreRequest();
        $transaction = $transactionService->createTransaction($request);

        return $this->success(['internal_id' => $transaction->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
