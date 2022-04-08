<?php

namespace App\Services\Transactions;

use App\Models\Bank;
use Exception;
use Carbon\Carbon;
use App\Models\Merchant;
use App\Exceptions\Errors;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Exceptions\ApiError;
use App\Services\TokenDecodingService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;

class TransactionByRequest
{
    /**
     *  Token decoder.
     *
     * @var TokenDecodingService
     */
    protected $tokenDecoder;

    /**
     * Request.
     *
     * @var Request.
     */
    protected $request;

    /**
     * Merchant.
     *
     * @var Merchant
     */
    protected $merchant;

    /**
     * Constructor.
     *
     * @param TokenDecodingService $tokenDecoder
     */
    public function __construct(TokenDecodingService $tokenDecoder)
    {
        $this->tokenDecoder = $tokenDecoder;
    }

    /**
     * Set request.
     *
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
        $this->setMerchantOfRequest();
    }

    /**
     * Return requested transactions.
     *
     * @return Collection
     */
    public function getTransactions(): Collection
    {
        $this->checkRequestExistence();

        return Transaction::query()
            ->where('merchant_id', $this->merchant->id)
            ->where('external_id', $this->request->id)
            ->get();
    }

    /**
     * Create transaction.
     *
     * @throws ApiError
     *
     * @return Transaction
     */
    public function createTransaction(): Transaction
    {
        $this->checkRequestExistence();

        if ($this->getTransactions()->count() > 0) {
            throw new ApiError(Errors::CODE_0400005);
        }

        $cardNumber = $this->getNormalizedCardNumber();

        $transactionData = [
            'date' => $this->getNormalizedDateOfTransaction(),
            'amount' => $this->getNormalizedAmountOfTransaction(),
            'card_number' => $cardNumber,
            'external_id' => $this->request->id,
            'merchant_id' => $this->merchant->id,
            'bank_id' => $this->getBankId($cardNumber),
        ];

        return Transaction::create($transactionData);
    }

    /**
     * Throw an exception if request has not been set.
     *
     * @throws Exception
     *
     * @return void
     */
    protected function checkRequestExistence(): void
    {
        if (!$this->request) {
            throw new Exception('Request has not been set.');
        }
    }

    /**
     * Validate store request.
     *
     * @throws ApiError
     *
     * @return void
     */
    public function validateStoreRequest(): void
    {
        $validator = Validator::make($this->request->all(), [
            'amount' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ((float) $value <= 0) {
                        $fail(Errors::CODE_0400007);
                    }
                }
            ],
            'card_number' => 'required',
            'id' => 'required',
            'token' => 'required'
        ], [
            'amount.required' => Errors::CODE_0400001,
            'amount.numeric' => Errors::CODE_0400006,
            'card_number.required' => Errors::CODE_0400002,
            'token.required' => Errors::CODE_0400003,
            'id.required' => Errors::CODE_0400004
        ]);

        if ($validator->fails()) {
            throw new ApiError($validator->errors()->first());
        }
    }

    /**
     * Set merchant.
     */
    protected function setMerchantOfRequest(): void
    {
        $this->merchant = $this->getMerchantRequest($this->request);
    }

    /**
     * Return merchant of the request.
     *
     * @return Merchant
     */
    protected function getMerchantRequest(): Merchant
    {
        if ($merchant = $this->tokenDecoder->getMerchantOfTransaction($this->request->id, $this->request->token)) {
            return $merchant;
        }

        throw new ApiError(Errors::CODE_0403001);
    }

    /**
     * Return date of an incoming transaction.
     *
     * @param  Request $request
     *
     * @return Carbon
     */
    protected function getNormalizedDateOfTransaction(): Carbon
    {
        return $this->request->date
            ? Carbon::parse($this->request->date)
            : Carbon::now()->addHours(3); // UTC+3
    }

    /**
     * Return amount of an incoming transaction.
     *
     * @return float
     */
    protected function getNormalizedAmountOfTransaction(): float
    {
        return round((float) $this->request->amount, 2);
    }

    /**
     * Return card number.
     *
     * @return string
     */
    protected function getNormalizedCardNumber(): string
    {
        return str_replace(' ', '', $this->request->card_number);
    }

    /**
     * @param string $value
     * @return string|null
     */
    protected function getBankId(string $value): ?string
    {
        $val = substr($value, 0, 6);

        return Bank::where('bin', $val)->value('id') ?? null;
    }
}
