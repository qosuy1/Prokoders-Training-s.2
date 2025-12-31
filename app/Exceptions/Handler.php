<?php
namespace App\Exceptions;


use Throwable;
use App\Helper\V1\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionsHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Handler extends ExceptionsHandler
{
    /**
     * Register exception handling callbacks.
     */
    public function register(): void
    {
        //
    }

    /**
     * Handle unauthenticated users (auth:sanctum).
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'message' => 'Unauthenticated.'
        ], 401);
    }

    public function render($request, Throwable $exception)
    {

        /* ───────────────────────────────
        | Spatie Role / Permission Error
        ─────────────────────────────── */
        if ($exception instanceof UnauthorizedException) {
            return ApiResponse::forbidden('You are not allowed to perform this action.');
        }

        /* ───────────────────────────────
         | Validation Errors (API)
         ─────────────────────────────── */
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], 422);
        }

        if($exception instanceof AccessDeniedHttpException)
            return ApiResponse::unauthorized('you cann\'t access this action');

        return parent::render($request , $exception);
    }
}
