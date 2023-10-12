<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use App\Helpers\SuccessMsg;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
            'passwordConfirmation' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error(ErrorCode::VALIDATION_FAILED, $validator->errors());
        }

        if ($data['password'] != $data['passwordConfirmation']) {
            return ApiResponse::error(ErrorCode::PASSWORD_NOT_MATCHED);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        //$user->createAsStripeCustomer();

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], SuccessMsg::USER_REGISTER_SUCCESS);
    }

    public function login(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error(ErrorCode::VALIDATION_FAILED, $validator->errors());
        }

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return ApiResponse::error(ErrorCode::PASSWORD_NOT_MATCHED);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], SuccessMsg::USER_LOGIN_SUCCESS);
    }

    public function logout(Request $request)
    {
        try {
            // 尝试撤销用于当前请求认证的令牌...
            $request->user()->currentAccessToken()->delete();

            // 如果成功，返回成功响应
            return ApiResponse::success([], SuccessMsg::USER_LOGOUT_SUCCESS); // 注意：您可能需要将消息更改为“注销成功”而不是“登录成功”
        } catch (\Exception $e) {
            // 捕获任何异常并记录，然后返回错误响应
            Log::error($e->getMessage());
            // 返回一个通用的错误消息
            // 注意：最好不要在生产环境中向用户展示具体的异常信息，因为这可能会暴露系统细节
            return ApiResponse::error('注销过程中出现问题，请稍后再试。', $e->getCode()); // 可以根据实际情况自定义错误消息和代码
        }
    }


    public function user(Request $request)
    {
        return ApiResponse::success([
            'user' => $request->user()
        ], SuccessMsg::OPERATION_SUCCESSFUL);
    }

    // create a new controller for user update with laravel sanctum
    public function update(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'string|unique:users',
            'email' => 'string|email|unique:users',
            'password' => 'string',
            'passwordConfirmation' => 'string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error(ErrorCode::VALIDATION_FAILED, $validator->errors());
        }

        if ($data['password'] != $data['passwordConfirmation']) {
            return ApiResponse::error(ErrorCode::PASSWORD_NOT_MATCHED);
        }

        $user = $request->user();

        if ($data['name']) {
            $user->name = $data['name'];
        }

        if ($data['email']) {
            $user->email = $data['email'];
        }

        if ($data['password']) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return ApiResponse::success([
            'user' => $user
        ], SuccessMsg::OPERATION_SUCCESSFUL);
    }

    // // make a new controller for user delete with laravel sanctum
    // public function delete(Request $request)
    // {
    //     $request->user()->delete();

    //     return ApiResponse::success([
    //     ], SuccessMsg::OPERATION_SUCCESSFUL);
    // }
}








