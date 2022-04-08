<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Extend base controller.
 */
class ApiController extends Controller
{
    /**
     * Return successful response.
     *
     * @param  array|null $payload
     *
     * @return JsonResponse
     */
    protected function success($payload = null): JsonResponse
    {
        $response = [
            'status' => 'ok',
        ];

        if (is_array($payload)) {
            $response['data'] = $payload;
        }

        return response()->json($response);
    }
}
