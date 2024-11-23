<?php

namespace App\Exceptions;

use Exception;

/**
 * Custom exception for handling cases where a task is not found.
 *
 * This exception is thrown when a task is requested but cannot be found in the database.
 */
class TaskNotFoundException extends Exception
{
    /**
     * Render an HTTP response when this exception is triggered.
     *
     * This method returns a JSON response with an error message and a 404 status code,
     * indicating that the task was not found.
     *
     * @param \Illuminate\Http\Request $request The current HTTP request.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the error message and status code.
     */
    public function render($request)
    {
        return response()->json([
            'error' => 'Task not found'
        ], 404);
    }
}
