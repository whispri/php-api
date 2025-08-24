<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e) {
            $statusCode = getHttpStatusCode($e);
            $message =  $e->getMessage() ?? INTERNAL_SERVER_ERROR;
            $data = config('app.env') === 'local' ? $e->getTrace() : null;
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        });
    }
}
