<?php
namespace App\Http\Controllers;

class ResponseFormat
{
    public static function success($message = null, $data = []) {
        return response()->json(['data' => $data, 'message' => $message], 200);
    }

    public static function error($message, $code = 400) {
        return response()->json(['message' => $message], $code);
    }
}
