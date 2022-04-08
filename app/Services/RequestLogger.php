<?php

namespace App\Services;

use Carbon\Carbon;
use Str;
use Log;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

/**
 * Request logger.
 */
class RequestLogger
{
    /**
     * ID of request.
     *
     * @var string
     */
    protected $requestId;

    /**
     * Incoming request.
     *
     * @var Request
     */
    protected $request;

    /**
     * Our response.
     *
     * @var JsonResponse
     */
    protected $response;

    /**
     * Error object.
     *
     * @var Throwable
     */
    protected $error;

    /**
     * Set request.
     *
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->requestId = Str::uuid();
        $this->request = $request;

        $this->request->attributes->set('request_id', $this->requestId);
    }

    /**
     * Set response.
     *
     * @param Request $request
     */
    public function setResponse(JsonResponse $response): void
    {
        $this->response = $response;
    }

    /**
     * Set error.
     *
     * @param Throwable $e
     */
    public function setError(Throwable $e): void
    {
        $this->error = $e;
    }


    /**
     * Write information about incoming request.
     *
     * @return void
     */
    public function writeRequest(): void
    {
        $logRecord = [
            'Date' => Carbon::now()->format('d.m.Y H:i:s'),
            'IP' => $this->request->ip(),
            'User-Agent' => $this->request->userAgent(),
            'Method' => $this->getRequestedResource(),
            'Params' => $this->request->all(),
        ];

        Log::channel('api-requests')->info($this->requestId, $logRecord);
    }

    /**
     * Write information about our response.
     *
     * @return void
     */
    public function writeResponse(): void
    {
        $logRecord = [
            'Response' => $this->getResponse()
        ];

        if ($this->error) {
            $logRecord['Error'] = $this->error->getMessage();
        }

        Log::channel('api-requests')->info($this->requestId, $logRecord);
    }

    /**
     * Return requested API method.
     *
     * @return string
     */
    protected function getRequestedResource(): string
    {
        $searchString = '/api/';
        $route = substr(
            $this->request->url(),
            stripos($this->request->url(), $searchString) + strlen($searchString) - 1
        );

        return $this->request->method() . ' ' . $route;
    }

    /**
     * Return response data.
     *
     * @return array
     */
    protected function getResponse(): array
    {
        $data = [
            'status' => data_get($this->response->original, 'status', 'badly formed response'),
            'data' => data_get($this->response->original, 'data', []),
        ];

        if ($data['status'] === 'error') {
            $data['code'] = $this->response->original['code'];
        }

        return $data;
    }
}
