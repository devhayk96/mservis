<?php

namespace App\Services\Processing\ResponseHandlers;

use App\Models\Transaction;
use App\Services\Processing\Logger;
use Psr\Http\Message\ResponseInterface;
use App\Services\MerchantTransactionCommission;

/**
 * Handle Payment response.
 */
abstract class BasePaymentResponseHandler implements PaymentResponseHandlerInterface
{
    /**
     * Transaction model.
     *
     * @var Transaction
     */
    protected $transaction;

    /**
     * Number of a request.
     *
     * @var int
     */
    protected $requestNumber;

    /**
     * Max number of requests we want to make to payment altogether.
     *
     * @var int
     */
    protected $maxAttempts;

    /**
     * Logger.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param string $requestId
     * @param int    $transactionId
     * @param string $requestDelays
     */
    public function __construct(string $requestId, int $transactionId, string $requestDelays)
    {
        $this->setTransaction($transactionId);
        $this->logger = new Logger($requestId, $transactionId);
        $this->maxAttempts = $this->setMaxAttempts($requestDelays);
        $this->commissionService = new MerchantTransactionCommission($this->transaction);
    }

    /**
     * Handle response from processing operator.
     *
     * @param  ResponseInterface $HttpClientResponse
     *
     * @return void
     */
    abstract public function handleResponse(ResponseInterface $HttpClientResponse): void;

    /**
     * @param int $transactionId
     */
    public function setTransaction(int $transactionId): void
    {
        $this->transaction = Transaction::with('merchant', 'commission')->find($transactionId);
    }

    /**
     * Set request number.
     *
     * @param int $requestNumber
     */
    public function setRequestNumber(int $requestNumber): void
    {
        $this->requestNumber = $requestNumber;
    }

    /**
     * Set max number of attempts.
     *
     * @param string $requestDelays
     * @return int
     */
    protected function setMaxAttempts(string $requestDelays): int
    {
        return app('subsequent-requests')->getAttemptsAmount($requestDelays) + 1;
    }

    /**
     * Prepare XML from payment response.
     *
     * @param  ResponseInterface $HttpClientResponse
     *
     * @return string
     */
    protected function prepareXMLFromHttpClientResponse(ResponseInterface $HttpClientResponse): string
    {
        $text = trim((string) $HttpClientResponse->getBody());
        $text = html_entity_decode($text, ENT_XML1, 'UTF-8');

        return $text;
    }
}
