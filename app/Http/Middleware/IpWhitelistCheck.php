<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\ApiError;
use App\Exceptions\Errors;

/**
 * Check whether IP of an incoming request is in the whitelist.
 */
class IpWhitelistCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string   $group
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $group = 'api')
    {
        if (app('ip-checker')->ipInWhitelist($request->ip())) {
            return $next($request);
        }

        if ($this->isApi($group)) {
            throw new ApiError(Errors::CODE_0401001);
        } else {
            abort(403);
        }
    }

    /**
     * Check whether request if for the API.
     *
     * @param  string $group
     *
     * @return bool
     */
    protected function isApi(string $group): bool
    {
        return $group === 'api';
    }
}
