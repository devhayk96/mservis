<?php

namespace App\Services\Processing\ResponseHandlers;

use Exception;
use SimpleXMLElement;
use App\Services\Processing\Logger;
use Psr\Http\Message\ResponseInterface;
use App\Services\Processing\Processors\PompayProcessor;

/**
 * Handle Pompay response.
 */
class PompayHandler extends BasePaymentResponseHandler
{
    /**
     * ID state of a success payment.
     */
    private const PAYMENT_STATE_SUCCESS = 60;

    /**
     * ID state of a failed payment.
     */
    private const PAYMENT_STATE_FAIL = 80;

    /**
     * ID state of a trying payment.
     */
    private const PAYMENT_STATE_TRY = 40;

    /**
     * ID state not found transaction.
     */
    private const PAYMENT_STATE_NOT_FOUND = "-2";

    /**
     * Constructor.
     *
     * @param string $requestId
     * @param int    $transactionId
     */
    public function __construct(string $requestId, int $transactionId)
    {
        parent::__construct($requestId, $transactionId, app('database-config')->get('pompay.check_request_delays'));
    }

    /**
     * Handle Pompay response.
     *
     * @param  ResponseInterface $HttpClientResponse
     *
     * @return void
     */
    public function handleResponse(ResponseInterface $HttpClientResponse): void
    {
        $responseBody = $this->prepareXMLFromHttpClientResponse($HttpClientResponse);

        $this->logger->logRequestStatus('Got response from Pompay.', [
            'body' => Logger::removeLineBreaks($responseBody)
        ]);

        try {
            $response = new SimpleXMLElement($responseBody);
        } catch (Exception $e) {
            $this->logger->logProcessingError(sprintf('Failed to parse XML response. %s.', $e->getMessage()), [
                'body' => Logger::removeLineBreaks($responseBody),
            ]);

            $this->checkAnotherRequest();
            return;
        }

        if ($response->result['state'] == self::PAYMENT_STATE_SUCCESS) {
            $this->transactionHandle($response, $responseBody, true);
        } elseif ($response->result['state'] == self::PAYMENT_STATE_FAIL) {
            $this->transactionHandle($response, $responseBody);
        } elseif ($response->result['state'] == self::PAYMENT_STATE_TRY) {
            $this->logger->logRequestStatus($this->responseMessage($response), [
                'response' => Logger::removeLineBreaks($responseBody),
            ]);

            $this->checkAnotherRequest(sprintf("Job is failed! Final response with error %s. ", $this->responseMessage($response)));
        } elseif ($response->result['state'] == self::PAYMENT_STATE_NOT_FOUND) {
            $this->transactionHandle($response, $responseBody);
            $this->logger->logProcessingError(sprintf("Job is failed! Final response with error %s. ", $this->responseMessage($response)));
        } else {
            $this->logger->logProcessingError(sprintf("Response with error: not found state"));
            $this->checkAnotherRequest(sprintf("Response with error: not found state"));
        }
    }

    /**
     * @param false $message
     */
    protected function checkAnotherRequest($message = false): void
    {
        if ($this->requestNumber < $this->maxAttempts) {
            $this->makeAnotherRequestLater();
        } else {
            if ($message) {
                $this->logger->logProcessingError($message);
            }
            $this->transaction->setAsFail();
            $this->transaction->markAsNotInProcessing();
            $this->transaction->save();
        }
    }

    /**
     * Make another request later.
     *
     * @return void
     */
    protected function makeAnotherRequestLater(): void
    {
        $nextAttemptNumber = $this->requestNumber + 1;
        (new PompayProcessor())->checkPayment($this->transaction, $nextAttemptNumber, app('database-config')->get('pompay.check_request_delays'));
    }

    /**
     * @param $response
     * @param $responseBody
     * @param false $success
     */
    private function transactionHandle($response, $responseBody, $success = false): void
    {
        if ($success) {
            $this->transaction->setAsSuccess();
        } else {
            $this->transaction->setAsFail();
        }

        $this->transaction->markAsNotInProcessing($this->responseMessage($response));
        $this->transaction->save();

        if ($this->commissionService->commissionHasNotBeenApplied()) {
            $this->commissionService->reduceMerchantBalance();
        }

        $this->logger->logRequestStatus($this->responseMessage($response), [
            'response' => Logger::removeLineBreaks($responseBody),
        ]);
    }

    /**
     * Return response messages.
     *
     * @param  SimpleXMLElement $response
     *
     * @return string
     */
    protected function responseMessage(SimpleXMLElement $response): string
    {
        $message = '';

        switch ($response->result['state']) {
            case self::PAYMENT_STATE_SUCCESS:
                $message = 'Payment made successfully.';
                break;
            case self::PAYMENT_STATE_FAIL:
                if ($response->result->attribute && ((string) $response->result->attribute['name']) === 'error-description') {
                    $message = (string) $response->result->attribute['value'];
                } else {
                    $message = 'Payment failed.';
                }
                break;
            case self::PAYMENT_STATE_TRY:
                $message = 'Trying to make a payment.';
                break;
            case self::PAYMENT_STATE_NOT_FOUND:
                $message = 'Payment not found.';
                break;
            default:
                $message = 'Something went wrong.';
                break;
        }

        return $message;
    }
}
