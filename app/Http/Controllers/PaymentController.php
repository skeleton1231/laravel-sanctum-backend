<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\SuccessMsg;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // show the payment methods for the current user with json response
        // return response()->json([
        //     'payment_methods' => $request->user()->paymentMethods,
        // ]);
        return ApiResponse::success([
            'payment_methods' => $request->user()->paymentMethods,
        ], SuccessMsg::OPERATION_SUCCESSFUL);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // price_1NMlZeDSmzjzdNYA59hyCrlw
        // Payment Create
        $request->user()->createAsStripeCustomer();

        // create a new payment method for the current user ApiResponse::success or error
        return ApiResponse::success([
            'payment_methods' => $request->user()->paymentMethods,
        ], SuccessMsg::OPERATION_SUCCESSFUL);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // app/Http/Controllers/PaymentController.php

    public function paypalSuccess(Request $request)
    {
        // Here you can handle the successful PayPal payment.
        // PayPal will send the payerID and paymentID to this URL after the user approves the payment.
        // You should validate these details and process the payment.
        // You can use the PayPal API to execute the payment using these IDs.
        // Then update your database with the payment details.
    }

    public function cancelPayment(Request $request)
    {
        // This is where you handle when a user cancels the payment on the PayPal page.
        // You can redirect the user to a specific page or show a message.
    }
}
