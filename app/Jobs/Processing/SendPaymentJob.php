<?php

namespace App\Jobs\Processing;

use Str;
use App\Services\Processing\Logger;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class SendPaymentJob
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    protected $tries;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    protected $timeout = 30;

    /**
     * Message
     *
     * @var string
     */
    protected $message;

    /**
     * ID of a transaction in processing.
     *
     * @var int
     */
    protected $transactionId;

    /**
     * ID of a request.
     *
     * @var string
     */
    protected $requestId;

    /**
     * Number of a request.
     *
     * @var int|null
     */
    protected $requestNumber;

    /**
     * Logger.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Transaction processor.
     *
     * @var \App\Services\Processing\Processors\ProcessorInterface
     */
    protected $processor;

    /**
     * Response handler.
     *
     * @var \App\Services\Processing\ResponseHandlers\PaymentResponseHandlerInterface
     */
    protected $responseHandler;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $transactionId, string $message, int $requestNumber, string $jobsDelays)
    {
        $requestId = (string) Str::uuid();

        $this->tries = $this->getAmountOfTries($jobsDelays);
        $this->message = $message;
        $this->transactionId = $transactionId;
        $this->requestId = $requestId;
        $this->requestNumber = $requestNumber;
        $this->logger = new Logger($this->requestId, $this->transactionId);

        $this->processor = $this->getProcessor();
        $this->responseHandler = $this->getResponseHandler();
    }

    /**
     * Return processor of a transaction.
     *
     * @return \App\Services\Processing\Processors\ProcessorInterface
     */
    abstract protected function getProcessor();

    /**
     * Return API response handler.
     *
     * @return \App\Services\Processing\ResponseHandlers\PaymentResponseHandlerInterface
     */
    abstract protected function getResponseHandler();

    /**
     * Handler for failed jobs.
     *
     * @param  Exception|Error $e
     *
     * @return void
     */
    public function failed($e)
    {
        $this->logger->logRequestStatus('Job is failed.');
        $this->processor->markTransactionAsNotInProcessing($this->transactionId);
    }

    /**
     * Return job delay time (in seconds).
     *
     * @param string $jobDelays
     * @return int
     */
    protected function getDelay(string $jobDelays): int
    {
        $delayInMinutes = app('subsequent-requests')->getDelay($jobDelays, $this->attempts());
        $secondsInMinute = 60;
        return $delayInMinutes * $secondsInMinute;
    }

    /**
     * Return amount of tries for this job.
     *
     * @param string $jobDelays
     * @return int
     */
    protected function getAmountOfTries(string $jobDelays): int
    {
        return app('subsequent-requests')->getAttemptsAmount($jobDelays) + 1;
    }

    /**
     * Get timeout value for request to API.
     *
     * @return int
     */
    protected function getRequestTimeout(): int
    {
        return abs($this->timeout - 5) ?: 10;
    }
}
