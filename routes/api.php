<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('sanctum/csrf-cookie', function () {
    return response()->json(['csrfToken' => csrf_token()]);
});


Route::prefix('v1/user')->group(function () {
    Route::middleware('auth:sanctum')->get('/', function (Request $request) {
        return $request->user();
    });

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function() {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
})->middleware('logrequest');;

// Paypal
// Route::get('/payments/paypal/success/{plan_id}', 'PaymentController@paypalSuccess')->name('payments.paypal.success');
// Route::get('/payments/paypal/cancel', 'PaymentController@paypalCancel')->name('payments.paypal.cancel');

// Route::middleware('auth')->group(function () {
//     Route::post('/subscriptions/create', [SubscriptionController::class, 'create']);
//     Route::post('/subscriptions/create', [SubscriptionController::class, 'create']);
// });

Route::middleware("uth:sanctum")->group(function () {
    Route::get('plans', [PlanController::class, 'index']);
    Route::get('plans/{plan}', [PlanController::class, 'list'])->name("plans.list");
    Route::post('subscription', [PlanController::class, 'subscription'])->name("subscription.create");
});

Route::middleware('auth:sanctum')->get('/billing-portal', [BillingController::class, 'billingPortal']);
