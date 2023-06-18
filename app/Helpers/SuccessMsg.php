<?php

namespace App\Helpers;
use Illuminate\Validation\Rules\Enum;

class SuccessMsg
{

    const OPERATION_SUCCESSFUL = 100000;
    const USER_REGISTER_SUCCESS  = 100001;
    private static $messages = [
        self::OPERATION_SUCCESSFUL => 'Operation successful',
        self::USER_REGISTER_SUCCESS => 'User registration successful',
        // ... add more success messages
    ];

    public static function get(string $key): string
    {
        return self::$messages[$key] ?? 'Unknown success message';
    }
}
