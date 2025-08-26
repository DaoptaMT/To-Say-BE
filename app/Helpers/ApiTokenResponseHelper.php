<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiTokenResponseHelper
{
    /**
     * Generate a successful API response for token generation.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    public static function tokenSuccess($data = [], string $message = 'Token generated successfully', int $status = 200): JsonResponse
    {
        $formattedData = is_array($data) ? $data : ['token' => $data];

        return response()->json([
            'status' => 'success',
            'data' => $formattedData,
            'message' => $message,
            'status_code' => $status,
        ], $status);
    }

    /**
     * Generate an error API response for token generation.
     *
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    public static function tokenError(string $message = 'Token generation failed', int $status = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'data' => null,
            'message' => $message,
            'status_code' => $status,
        ], $status);
    }
}