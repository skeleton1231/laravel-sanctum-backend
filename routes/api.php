<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\BillingController;
use App\Helpers\ApiResponse;
use App\Helpers\SuccessMsg;


/*
|--------------------------------------------------------------------------
| API Routes
|-------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('sanctum/csrf-cookie', function () {
    return response()->json(['csrfToken' => csrf_token()]);
});

Route::get('/test', [TestController::class, 'index']);


Route::prefix('v1/user')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    //Route::post('/logout', [AuthController::class, 'logout']);

    // 定义一个中间件组
    Route::middleware(['auth:sanctum'])->group(function () {
        // 所有在这个组里的路由都会通过指定的中间件
        Route::get('/', function (Request $request) {
            return $request->user();
        });

        Route::post('/logout', [AuthController::class, 'logout']);
    });
})->middleware('logrequest');



Route::middleware("auth:sanctum")->group(function () {
    Route::get('/plans', [PlanController::class, 'index']);
    Route::get('/plans/{plan}', [PlanController::class, 'list'])->name("plans.list");
    Route::post('/subscription', [PlanController::class, 'subscription'])->name("subscription.create");
    Route::post('/create-subscription', [SubscriptionController::class, 'create']);
    Route::get('/billing-portal', [BillingController::class, 'billingPortal']);

    // 在你的 web.php 路由文件中
    Route::get('/setup-intent', function (Request $request) {
        // 确认用户已登录，并获取已认证的用户
        $user = $request->user();
        return ApiResponse::success([
            'intent' => $user->createSetupIntent(),
        ], SuccessMsg::OPERATION_SUCCESSFUL);
        // 创建一个新的 setup intent
        // return response()->json([
        //     'intent' => $user->createSetupIntent()
        // ]);
    });
});
