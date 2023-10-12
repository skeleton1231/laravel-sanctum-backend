<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException; // 正确导入 AuthenticationException 类


class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // 检查异常是否是认证异常
        if ($exception instanceof AuthenticationException) {
            // 是认证异常，你可以在这里返回自定义的错误响应
            return  ApiResponse::error(ErrorCode::AUTH_TOKEN_INVALID);
            //return response()->json(['message' => 'Unauthenticated'], 401);
        }
        // 对于其他类型的异常，继续使用默认的处理逻辑
        return parent::render($request, $exception);
    }
}
