<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckToken
{
    public function handle(Request $request, Closure $next)
    {
         // 记录一个信息日志来表示此中间件已被触发
         Log::info('CheckToken middleware triggered');

        // 检查是否有已认证的用户
        if ($request->user()) {
            Log::info('User is authenticated'); // 如果用户已认证，记录信息
            // 获取与请求相关联的当前 token
            $token = $request->user()->currentAccessToken();

            if ($token) {
                // 在这里，你可以执行更多的检查，例如检查 token 的特定权限或角色
                Log::info('Token is valid', ['token_id' => $token->id]); // 记录带有 token 信息的日志

                // 如果一切正常，继续下一个请求
                return $next($request);
            }
        }

        // 如果用户未认证或 token 不存在，返回一个 401 错误
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
