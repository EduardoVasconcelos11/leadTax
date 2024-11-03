<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function apiResponse($message, $data = null, $status_code = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'status_code' => $status_code
        ], $status_code);
    }
}
