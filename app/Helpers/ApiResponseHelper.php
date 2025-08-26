<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponseHelper
{
    /**
     * Generate a successful API response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    public static function success($data = null, $message = 'Operation successful', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'status_code' => $status,
        ], $status);
    }

    /**
     * Generate an error API response.
     *
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    public static function error($message = 'An error occurred', int $status = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'data' => null,
            'message' => $message,
            'status_code' => $status,
        ], $status);
    }
}