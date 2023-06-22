<?php

namespace App\Helpers;
use Illuminate\Validation\Rules\Enum;

class SuccessMsg
{

    const OPERATION_SUCCESSFUL = 100000;
    const USER_REGISTER_SUCCESS  = 100001;

    const USER_LOGIN_SUCCESS = 100002;
    const USER_LOGOUT_SUCCESS = 100003;
    private static $messages = [
        self::OPERATION_SUCCESSFUL => 'Operation successful',
        self::USER_REGISTER_SUCCESS => 'User registration successful',
        self::USER_LOGIN_SUCCESS => 'User login successful',
        // ... add more success messages
    ];

    public static function get(string $key): string
    {
        return self::$messages[$key] ?? 'Unknown success message';
    }
}
