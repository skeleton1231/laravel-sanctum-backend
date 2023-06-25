<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\SuccessMsg;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function billingPortal(Request $request)
    {
        $url = $request->user()->billingPortalUrl(route('billing'));
        return ApiResponse::success([
            'url' => $url,
        ],SuccessMsg::OPERATION_SUCCESSFUL);
    }
}
