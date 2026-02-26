<?php

namespace App\Helpers;

class ResponseHelper
{
    /**
     * Return a standardized API response.
     *
     * @param int $statusCode
     * @param string $message
     * @param mixed $errors
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function format($statusCode = 200, $message = 'Success', $errors = null, $data = [])
    {
        return response()->json([
            'status_code' => $statusCode,
            'message' => $message,
            'errors' => $errors,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return a standardized success API response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = [], $message = 'Success', $statusCode = 200)
    {
        return self::format($statusCode, $message, null, $data);
    }

    /**
     * Return a standardized error API response.
     *
     * @param string $message
     * @param mixed $errors
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($message = 'Error', $errors = null, $statusCode = 400)
    {
        return self::format($statusCode, $message, $errors, []);
    }
}
