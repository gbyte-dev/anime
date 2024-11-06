<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServerErrorHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Throwable;
use Log;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // Log the exception
            Log::error($e->getMessage(), ['exception' => $e]);
        });

        $this->renderable(function (Exception $e, Request $request) {
            // Handle Not Found (404)
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => 'Resource not found.'
                ], 404);
            }

            // Handle Server Error (500)
            if ($e instanceof ServerErrorHttpException) {
                return response()->json([
                    'message' => 'Internal server error. Please try again later.'
                ], 500);
            }

            // Handle Service Unavailable (503)
            if ($e instanceof ServiceUnavailableHttpException) {
                return response()->json([
                    'message' => 'Service unavailable. Please try again later.'
                ], 503);
            }

            // Default behavior for other exceptions
            return parent::render($request, $e);
        });
    }
}
