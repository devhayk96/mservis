<?php

namespace App\Jobs\Processing;

use App\Enums\ProcessingOperatorsEnum;
use Exception;
use GuzzleHttp\Client;
use App\Services\Processing\Logger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Services\Processing\Processors\ProcessorFactory;
use App\Services\Processing\ResponseHandlers\ArmaxHandler;

/**
 * Send requests to Armax's API.
 */
class ArmaxJob extends SendPaymentJob implements ShouldQueue, ShouldBeUnique
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $transactionId, string $message, int $requestNumber = 1)
    {
        parent::__construct($transactionId, $message, $requestNumber, app('database-config')->get('armax.failed_jobs_delays'));
    }

    /**
     * Return processor of a transaction.
     *
     * @return \App\Services\Processing\Processors\ProcessorInterface
     */
    public function getProcessor()
    {
        return ProcessorFactory::create(ProcessingOperatorsEnum::ARMAX);
    }

    /**
     * Return response handler.
     *
     * @return \App\Services\Processing\ResponseHandlers\PaymentResponseHandlerInterface
     */
    protected function getResponseHandler()
    {
        $responseHandler = new ArmaxHandler($this->requestId, $this->transactionId);
        $responseHandler->setRequestNumber($this->requestNumber);
        return $responseHandler;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logger->logRequestStatus('Sending request to Armax.', [
            'body' => Logger::removeLineBreaks($this->message)
        ]);

        $client = new Client([
            'base_uri' => 'https://pays-api-2012.armax.ru/pays-api2012/api/v1/',
            'timeout' => $this->getRequestTimeout(),
            'headers' => [
                'Content-Type' => 'text/plain;charset=utf-8'
            ]
        ]);

        try {
            $response = $client->request('POST', 'pays', ['body' => $this->message]);
        } catch (Exception $e) {
            $this->logger->logRequestError($e->getMessage());
            return $this->release($this->getDelay(app('database-config')->get('armax.failed_jobs_delays')));
        }

        $this->responseHandler->handleResponse($response);
    }
}
