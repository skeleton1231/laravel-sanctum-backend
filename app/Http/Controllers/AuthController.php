<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use App\Helpers\SuccessMsg;
use App\Models\User;
use Godruoyi\Snowflake\Snowflake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // 开始事务
        DB::beginTransaction();

        try {
            // $snowflake = app('Kra8\Snowflake\Snowflake');
            // $id = $snowflake->next();
            // Log::info("SnowflakeId: " . $id);
            // 尝试操作
            $user = User::create([
                //'id' => $id,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // 尝试创建 token
            $token = $user->createToken('auth_token')->plainTextToken;

            // 如果到达这里，说明上面的操作都成功了，我们可以提交事务
            DB::commit();

            // ... 返回成功响应 ...
        } catch (\Exception $e) {
            // 如果我们捕获到任何异常，我们需要回滚事务
            DB::rollBack();

            // 记录错误详情到日志文件
            Log::error('Registration failed: ' . $e->getMessage());

            // 返回错误响应
            return ApiResponse::error(ErrorCode::SERVER_INTERNAL_ERROR);
        }

        if ($user->id) {
            try {
                // 尝试创建 Stripe 客户
                $user->createAsStripeCustomer();
            } catch (\Exception $e) {
                // 记录错误详情到日志文件
                Log::error('Stripe create failed: ' . $e->getMessage());
            }
        }

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
