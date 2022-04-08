<?php

namespace App\Exceptions;

use App\Services\Transactions\TransactionEmail;
use Arr;
use Log;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        ApiError::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * A list of the exception types that are not renderable.
     *
     * @var array
     */
    protected $dontRender = [
        AuthenticationException::class,
        ValidationException::class,
        NotFoundHttpException::class,
        PostTooLargeException::class,
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $e) {
            app('request-logger')->setError($e);

            $this->shouldNotify($e);

            if ($this->shouldntRender($e)) {
                return;
            }

            if (config('app.render_custom_error_messages')) {
                return $this->getErrorResponse($e);
            }
        });
    }

    /**
     * Return error response.
     *
     * @param  Throwable $e [description]
     *
     * @return JsonResponse
     */
    protected function getErrorResponse(Throwable $e): JsonResponse
    {
        if ($e instanceof ApiError) {
            return $this->apiError($e);
        }

        return $this->generalError();
    }

    /**
     * Return API error response.
     *
     * @param  ApiError $e
     *
     * @return JsonResponse
     */
    protected function apiError(ApiError $e): JsonResponse
    {
        $this->logApiError($e);

        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'code' => $e->getErrorCode(),
        ], $e->getHttpStatus());
    }

    /**
     * Log about API error.
     *
     * @param  ApiError $e
     *
     * @return void
     */
    protected function logApiError(ApiError $e): void
    {
        Log::channel('api-errors')->debug($e->getMessage(), [
            'code' => $e->getErrorCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }

    /**
     * Return general error response.
     *
     * @return JsonResponse
     */
    protected function generalError(): JsonResponse
    {
        $code = Errors::CODE_0500001;
        $error = Errors::all()[$code];

        return response()->json([
            'status' => 'error',
            'message' => $error['message'],
            'code' => $code,
        ], $error['httpStatusCode']);
    }

    /**
     * Determine if the exception is in the "do not render" list.
     *
     * @param  Throwable  $e
     *
     * @return bool
     */
    protected function shouldntRender(Throwable $e): bool
    {
        if ($e instanceof HttpException && in_array($e->getStatusCode(), [403, 419])) {
            return true;
        }

        return ! is_null(Arr::first($this->dontRender, function ($type) use ($e) {
            return $e instanceof $type;
        }));
    }

    /**
     * @param Throwable $e
     */
    protected function shouldNotify(Throwable $e): void
    {
        if ($e instanceof HttpException && in_array($e->getStatusCode(), [429])) {
            $transactionMail = new TransactionEmail;
            $transactionMail->dispatch($e->getMessage());
        }
    }
}
