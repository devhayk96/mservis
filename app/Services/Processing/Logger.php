<?php

namespace App\Services\Processing;

use Log;

/**
 * Logging for transaction processing.
 */
class Logger
{
    /**
     * UUID of processing request.
     *
     * @var string
     */
    private $requestId;

    /**
     * ID of a transaction in process.
     *
     * @var int
     */
    private $transactionId;

    /**
     * Constructor.
     *
     * @param string $requestId
     * @param int    $transactionId
     */
    public function __construct(string $requestId, int $transactionId)
    {
        $this->requestId = $requestId;
        $this->transactionId = $transactionId;
    }

    /**
     * Log processing request status.
     *
     * @param  string $message
     * @param  array  $optionalData
     *
     * @return void
     */
    public function logRequestStatus(string $message, array $optionalData = []): void
    {
        Log::channel('processing-requests')->info($this->requestId, [
            'transactionId' => $this->transactionId,
            'message' => $message,
        ] + $optionalData);
    }

    /**
     * Log request (network) errors.
     *
     * @param  string $message
     * @param  array  $optionalData
     *
     * @return void
     */
    public function logRequestError(string $message, array $optionalData = []): void
    {
        Log::channel('processing-errors')->info($this->requestId, [
            'transactionId' => $this->transactionId,
            'errorType' => 'request',
            'errorMessage' => $message
        ] + $optionalData);
    }

    /**
     * Log processing errors.
     *
     * @param  string $message
     * @param  array  $optionalData
     *
     * @return void
     */
    public function logProcessingError(string $message, array $optionalData = []): void
    {
        Log::channel('processing-errors')->info($this->requestId, [
            'transactionId' => $this->transactionId,
            'errorType' => 'processing',
            'errorMessage' => $message,
        ] + $optionalData);
    }

    /**
     * Remove line breaks from a string.
     *
     * @param  string $text
     *
     * @return string
     */
    public static function removeLineBreaks(string $text): string
    {
        return str_replace(array("\r", "\n"), '', $text);
    }
}
