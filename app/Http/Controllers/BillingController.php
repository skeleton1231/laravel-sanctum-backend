<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingController extends Controller
{
    // show the billing list with laravel sanctum  and json response
    public function index(Request $request)
    {
        return response()->json([
            'billing' =>$request->user()->billing()
        ]);
    }
}
