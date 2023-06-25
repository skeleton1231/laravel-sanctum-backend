<?php


namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\SuccessMsg;
use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    /**
     * Write code on Method
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $plans = Plan::get();

        return ApiResponse::success([
            'plans' => $plans,
        ], SuccessMsg::OPERATION_SUCCESSFUL);
    }

    /**
     * Write code on Method
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Plan $plan, Request $request)
    {
        $intent = $request->user()->createSetupIntent();

        //return view("subscription", compact("plan", "intent"));
        return ApiResponse::success([
            'plan' => $plan,
            'intent' => $intent,
        ], SuccessMsg::OPERATION_SUCCESSFUL);
    }
    /**
     * Write code on Method
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscription(Request $request)
    {
        $plan = Plan::find($request->plan);

        $subscription = $request->user()->newSubscription($request->plan, $plan->stripe_plan)
            ->create($request->token);

        return ApiResponse::success([
            'subscription' => $subscription,
        ], SuccessMsg::OPERATION_SUCCESSFUL);
    }
}
