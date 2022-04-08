<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiError;
use App\Exceptions\Errors;

/**
 * Extend base controller.
 */
class NotFoundController extends Controller
{
    /**
     * General not found handler for the 'api' group routes.
     *
     * @throws ApiError
     *
     * @return void
     */
    public function notFoundForApi()
    {
        throw new ApiError(Errors::CODE_0404001);
    }

    /**
     * General not found handler for the 'web' group routes.
     *
     * @throws ApiError
     *
     * @return void
     */
    public function notFoundForWeb()
    {
        abort(404);
    }
}
