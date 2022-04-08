<?php

namespace App\Services\Processing\ResponseHandlers;

use Psr\Http\Message\ResponseInterface;

interface PaymentResponseHandlerInterface
{
    /**
     * Handle response from processing operator.
     *
     * @param  ResponseInterface $HttpClientResponse
     *
     * @return void
     */
    public function handleResponse(ResponseInterface $HttpClientResponse): void;
}
