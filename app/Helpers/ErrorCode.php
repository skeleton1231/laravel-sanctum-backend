<?php

namespace App\Helpers;

class ErrorCode
{
    private static $messages = [
        // 200
        self::USER_REGISTER_EMAIL_ALREADY_EXISTS => 'Email already exists',
        self::VALIDATION_FAILED => 'Validate Fail',
        self::PASSWORD_NOT_MATCHED=>'User Password does not match',
        // 400
        self::AUTH_TOKEN_INVALID => 'Authentication token invalid',
        self::AUTH_PERMISSION_DENIED => 'Permission denied',
        self::USER_NOT_FOUND => 'User not found',

        // 500
        self::SERVER_INTERNAL_ERROR => 'Internal server error',
    ];

    // Error Categories
    const SUCCESS_ERROR = 200000;
    const CLIENT_ERROR = 400000;
    const UNAUTHORIZED_ERROR = 401000;
    const FORBIDDEN_ERROR = 403000;
    const NOT_FOUND_ERROR = 404000;
    const SERVER_ERROR = 500000;

    // Success Error(200)
    const VALIDATION_FAILED = self::SUCCESS_ERROR + 1;
    const USER_REGISTER_EMAIL_ALREADY_EXISTS = self::SUCCESS_ERROR + 2;
    const PASSWORD_NOT_MATCHED = self::SUCCESS_ERROR + 3;




    // Client Error Series (400)

    // Unauthorized Series (401)
    const AUTH_TOKEN_INVALID = self::UNAUTHORIZED_ERROR + 1;

    // Forbidden Series (403)
    const AUTH_PERMISSION_DENIED = self::FORBIDDEN_ERROR + 1;

    // Not Found Series (404)
    const USER_NOT_FOUND = self::NOT_FOUND_ERROR + 1;

    // Server Error Series (500)
    const SERVER_INTERNAL_ERROR = self::SERVER_ERROR + 1;

    public static function getMessage(int $code): string
    {
        return self::$messages[$code] ?? 'Unknown error';
    }

    public static function getHttpStatus(int $code): int
    {
        $category = intdiv($code, 1000) * 1000;
        return match ($category) {
            self::SUCCESS_ERROR => 200,
            self::CLIENT_ERROR => 400,
            self::UNAUTHORIZED_ERROR => 401,
            self::FORBIDDEN_ERROR => 403,
            self::NOT_FOUND_ERROR => 404,
            self::SERVER_ERROR => 500,
            default => 500,
        };
    }
}
