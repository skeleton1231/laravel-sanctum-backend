<?php

namespace App\Helpers;

use App\Helpers\ErrorCode;
use App\Helpers\SuccessMsg;

class ApiResponse
{
    public static function success($data = [], $messageKey = 'OPERATION_SUCCESSFUL', $status = 200)
    {
        $message = SuccessMsg::get($messageKey);
        return response()->json([
            "data" => $data,
            "msg" => $message
        ], $status);
    }

    public static function error($code, $errors = null)
    {
        return response()->json([
            "errors" => [
                'code' => $code,
                'message' => ErrorCode::getMessage($code),
                'details' => $errors
            ]
        ], ErrorCode::getHttpStatus($code));
    }
}
