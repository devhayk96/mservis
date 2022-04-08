<?php

namespace App\Jobs\Processing;

use Exception;
use GuzzleHttp\Client;
use App\Services\Processing\Logger;
use App\Enums\ProcessingOperatorsEnum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Services\Processing\Processors\ProcessorFactory;
use App\Services\Processing\ResponseHandlers\PompayHandler;

/**
 * Send requests to Pompay's API.
 */
class PompayJob extends SendPaymentJob implements ShouldQueue, ShouldBeUnique
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $transactionId, string $message, int $requestNumber = 1)
    {
        parent::__construct($transactionId, $message, $requestNumber, app('database-config')->get('pompay.failed_jobs_delays'));
    }

    /**
     * Return processor of a transaction.
     *
     * @return \App\Services\Processing\Processors\ProcessorInterface
     */
    public function getProcessor()
    {
        return ProcessorFactory::create(ProcessingOperatorsEnum::POMPAY);
    }

    /**
     * Return response handler.
     *
     * @return \App\Services\Processing\ResponseHandlers\PaymentResponseHandlerInterface
     */
    protected function getResponseHandler()
    {
        $responseHandler = new PompayHandler($this->requestId, $this->transactionId);
        $responseHandler->setRequestNumber($this->requestNumber);
        return $responseHandler;
    }

    /**
     * Execute the job.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $privateKey = $this->removeBlankSpaces(app('database-config')->get('pompay.private_key'));
        $privateKeyPassword = app('database-config')->get('pompay.private_key_password');

        $signature = '';
        $keyId = openssl_get_privatekey($privateKey, $privateKeyPassword);
        openssl_sign($this->message, $signature, $keyId);

        $this->logger->logRequestStatus('Sending request to Pompay.', [
            'body' => Logger::removeLineBreaks($this->message)
        ]);

        $client = new Client([
            'base_uri' => 'https://ext1.uniteplat.ru:10433/payment-easy/',
            'timeout' => $this->getRequestTimeout(),
            'headers' => [
                'Signature' => base64_encode($signature),
                'Content-Type' => 'text/plain;charset=utf-8'
            ]
        ]);

        try {
            $response = $client->request('POST', app('database-config')->get('pompay.username'), ['body' => $this->message]);
        } catch (Exception $e) {
            $this->logger->logRequestError($e->getMessage());
            return $this->release($this->getDelay(app('database-config')->get('pompay.failed_jobs_delays')));
        }

        $this->responseHandler->handleResponse($response);
    }

    /**
     * Remove blank spaces from a string.
     *
     * @param  string $text
     *
     * @return string
     */
    protected function removeBlankSpaces(string $text): string
    {
        return str_replace(array("  "), '', $text);
    }
}
