<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Return a success response.
     */
    public function successResponse($data, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return an error response.
     */
    public function errorResponse($message, $code = 400, $data = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return a validation error response.
     */
    public function validationErrorResponse($errors, $message = 'Validation Error', $code = 422)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}
