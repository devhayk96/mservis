<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Log incoming API requests.
 */
class LogApiRequests
{
    protected $isApiRequest;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->isApiRequest = $this->requestIsForApi($request)) {
            app('request-logger')->setRequest($request);
            app('request-logger')->writeRequest();
        }

        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        if ($this->isApiRequest) {
            app('request-logger')->setResponse($response);
            app('request-logger')->writeResponse();
        }
    }

    /**
     * Determine if request is for API.
     *
     * @param  Request $request
     *
     * @return bool
     */
    protected function requestIsForApi(Request $request): bool
    {
        return strpos($request->url(), '/api/') !== false;
    }
}
