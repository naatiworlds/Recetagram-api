<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use App\Helpers\ResponseHelper;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            if ($exception instanceof ValidationException) {
                return ResponseHelper::error(
                    $exception->validator->errors()->first(),
                    422
                );
            }

            if ($exception instanceof AuthenticationException) {
                return ResponseHelper::error(
                    'Unauthenticated',
                    401
                );
            }

            // Manejo general de errores para la API
            return ResponseHelper::error(
                $exception->getMessage(),
                500
            );
        }

        return parent::render($request, $exception);
    }
}