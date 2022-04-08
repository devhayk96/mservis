<?php

namespace App\Exceptions;

use Exception;

/**
 * Error exception objects of the application.
 */
class ApiError extends Exception
{
    /**
     * Error code.
     *
     * @var string
     */
    protected $errorCode;

    /**
     * HTTP status code.
     *
     * @var int
     */
    protected $httpStatusCode;

    /**
     * Constructor.
     *
     * @param string $errorCode
     */
    public function __construct(string $errorCode)
    {
        $this->errorCode = $errorCode;
        $this->defineError();
    }

    /**
     * Return error code.
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Return HTTP status code.
     *
     * @return int
     */
    public function getHttpStatus(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * Set object properties.
     *
     * @return void
     */
    protected function defineError(): void
    {
        $error = $this->getError();
        $this->message = $error['message'];
        $this->httpStatusCode = $error['httpStatusCode'];
    }

    /**
     * Find appropriate error data.
     *
     * @return array
     */
    protected function getError(): array
    {
        $allErrors = Errors::all();
        return data_get($allErrors, $this->errorCode, $allErrors[Errors::CODE_0500002]);
    }
}
