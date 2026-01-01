<?php

namespace App\Exceptions;

use Throwable;
use App\Helper\V1\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionsHandler;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Handler extends ExceptionsHandler
{
    /**
     * Handle unauthenticated users (Sanctum).
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return ApiResponse::unauthorized('Unauthenticated.');
    }

    public function render($request, Throwable $exception)
    {
        // Apply ONLY for API routes
        if ($request->is('api/*')) {

            /* ───────────────────────────────
             | Model Not Found (Eloquent) – 404
             ─────────────────────────────── */
            if ($exception instanceof ModelNotFoundException) {
                return ApiResponse::notFound('Resource not found.');
            }

            /* ───────────────────────────────
             | Spatie Role / Permission – 403
             ─────────────────────────────── */
            if ($exception instanceof UnauthorizedException) {
                return ApiResponse::forbidden(
                    'You are not allowed to perform this action.'
                );
            }

            /* ───────────────────────────────
             | Validation – 422
             ─────────────────────────────── */
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $exception->errors(),
                ], 422);
            }

            /* ───────────────────────────────
             | Access Denied – 403
             ─────────────────────────────── */
            if ($exception instanceof AccessDeniedHttpException) {
                return ApiResponse::forbidden(
                    'You cannot access this action.'
                );
            }

            /* ───────────────────────────────
             | Route Not Found – 404
             ─────────────────────────────── */
            if ($exception instanceof NotFoundHttpException) {
                return ApiResponse::notFound('Endpoint not found.');
            }

            /* ───────────────────────────────
             | Fallback – 500
             ─────────────────────────────── */
            return ApiResponse::serverError('Internal server error.');
        }

        return parent::render($request, $exception);
    }
}
