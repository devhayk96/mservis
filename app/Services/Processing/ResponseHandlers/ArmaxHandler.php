<?php

namespace App\Services\Processing\ResponseHandlers;

use Exception;
use SimpleXMLElement;
use App\Services\Processing\Logger;
use Psr\Http\Message\ResponseInterface;
use App\Services\Processing\Processors\ArmaxProcessor;

/**
 * Handle Armax response.
 */
class ArmaxHandler extends BasePaymentResponseHandler
{
    /**
     * ID status of a success payment.
     */
    private const PAYMENT_STATUS_SUCCESS = 1;

    /**
     * ID status of a failed payment.
     */
    private const PAYMENT_STATUS_FAIL = 2;

    /**
     * Constructor.
     *
     * @param string $requestId
     * @param int    $transactionId
     */
    public function __construct(string $requestId, int $transactionId)
    {
        parent::__construct($requestId, $transactionId, app('database-config')->get('armax.check_request_delays'));
    }

    /**
     * Handle Armax response.
     *
     * @param  ResponseInterface $HttpClientResponse
     *
     * @return void
     */
    public function handleResponse(ResponseInterface $HttpClientResponse): void
    {
        $responseBody = $this->prepareXMLFromHttpClientResponse($HttpClientResponse);

        $this->logger->logRequestStatus('Got response from Armax.', [
            'body' => Logger::removeLineBreaks($responseBody)
        ]);

        try {
            $response = new SimpleXMLElement($responseBody);
        } catch (Exception $e) {
            if ($this->haveMoreAttempts()) {
                $this->logger->logProcessingError(sprintf('Failed to parse XML response. %s.', $e->getMessage()), [
                    'body' => Logger::removeLineBreaks($responseBody),
                ]);

                $this->makeAnotherRequestLater();
            } else {
                $this->logger->logProcessingError(sprintf("After a few attempts we still can't parse a response. Stop payment processing. %s", $e->getMessage()));
                $this->transaction->markAsNotInProcessing("Can't parse a response.");
            }

            return;
        }

        if ($this->responseWithoutError($response)) {
            $payment = $response->{$this->getPaymentTagName()}->payment;

            if ($this->paymentIsSuccess($payment)) {
                $this->transaction->setAsSuccess();
                $this->transaction->save();

                $this->transaction->markAsNotInProcessing();

                if ($this->commissionService->commissionHasNotBeenApplied()) {
                    $this->commissionService->reduceMerchantBalance();
                }

                $this->logger->logRequestStatus('Transaction marked as success.', [
                    'response' => Logger::removeLineBreaks($responseBody),
                ]);
            } elseif ($this->paymentIsFail($payment)) {
                $this->transaction->setAsFail();
                $this->transaction->save();

                $this->transaction->markAsNotInProcessing((string) $payment['description']);

                $this->logger->logRequestStatus('Transaction marked as failed.', [
                    'response' => Logger::removeLineBreaks($responseBody),
                ]);
            } else {
                if ($this->haveMoreAttempts()) {
                    $this->logger->logRequestStatus('Payment in process. We are waiting for next request.', [
                        'response' => Logger::removeLineBreaks($responseBody),
                    ]);
                    $this->makeAnotherRequestLater();
                } else {
                    $this->logger->logProcessingError(sprintf("Consider processing as failed and stop it. Payment result %s. %s", $payment['result'], $payment['result-description']));
                    $this->transaction->markAsNotInProcessing(sprintf('%s (result %s)', $payment['result-description'], $payment['result']));
                }
            }
        } else {
            if ($this->haveMoreAttempts()) {
                $this->logger->logProcessingError(sprintf("Response with error %s. %s.", $response['result'], $response['result-description']));
                $this->makeAnotherRequestLater();
            } else {
                $this->logger->logProcessingError(sprintf("Stop processing! Final response with error %s. %s", $response['result'], $response['result-description']));
                $this->transaction->markAsNotInProcessing(sprintf('%s (result %s)', $response['result-description'], $response['result']));
            }
        }
    }

    /**
     * Check if more attempts are available.
     *
     * @return bool
     */
    protected function haveMoreAttempts(): bool
    {
        return ($this->requestNumber < $this->maxAttempts);
    }

    /**
     * Make another request later.
     *
     * @return void
     */
    protected function makeAnotherRequestLater(): void
    {
        $nextAttemptNumber = $this->requestNumber + 1;
        (new ArmaxProcessor())->checkPayment($this->transaction, $nextAttemptNumber, app('database-config')->get('armax.check_request_delays'));
    }

    /**
     * Check if response has no errors.
     *
     * @param  SimpleXMLElement $response
     *
     * @return bool
     */
    protected function responseWithoutError(SimpleXMLElement $response): bool
    {
        return ((int) $response['result']) === 0;
    }

    /**
     * Return payment tag name.
     *
     * @return string
     */
    protected function getPaymentTagName(): string
    {
        if (ArmaxProcessor::isInTestMode()) {
            return 'check-payment';
        }

        return 'add-payment';
    }

    /**
     * Check if payment is success.
     *
     * @param  SimpleXMLElement $payment
     *
     * @return bool
     */
    protected function paymentIsSuccess(SimpleXMLElement $payment): bool
    {
        return ((int) $payment['status']) === self::PAYMENT_STATUS_SUCCESS;
    }

    /**
     * Check if payment is fail.
     *
     * @param  SimpleXMLElement $payment
     *
     * @return bool
     */
    protected function paymentIsFail(SimpleXMLElement $payment): bool
    {
        return ((int) $payment['status']) === self::PAYMENT_STATUS_FAIL;
    }
}
