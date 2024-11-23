<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use App\Models\ErrorLog;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        // Add exception types and their log levels if needed.
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        // Add exceptions that should not be reported.
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     * This allows registering custom reporting and rendering logic for exceptions.
     *
     * @return void
     */
    public function register(): void
    {
        // Custom reporting logic can be added here.
        $this->reportable(function (Throwable $e) {
            // Handle the reporting of the exception, if needed.
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function render($request, Throwable $exception)
    {
        // Log all exceptions to a log file for future debugging.
        Log::error('API Error: ' . $exception->getMessage());

        // Return a clear error message to the user if the request is an API request.
        if ($request->is('api/*')) {
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }

        // Default handling for non-API requests.
        return parent::render($request, $exception);
    }

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        // If the exception is an instance of a general Exception, save it in the custom ErrorLog model.
        if ($exception instanceof \Exception) {
            ErrorLog::create([
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }

        // Call the parent report method to handle the rest of the exceptions.
        parent::report($exception);
    }
}
