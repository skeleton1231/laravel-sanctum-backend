<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use App\Helpers\SuccessMsg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException; // Include this for Stripe's API exceptions
use Exception; // Include this for general PHP exceptions

class SubscriptionController extends Controller
{
    public function create(Request $request)
    {
        try {
            $user = $request->user();
            // Set Stripe secret key
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Create or retrieve a Stripe customer
            if ($user->stripe_id) {
                $customer = Customer::retrieve($user->stripe_id);
            } else {
                $customer = Customer::create([
                    'email' => $user->email,
                    // Add any other customer details you want to collect
                ]);
                // Saving the stripe_id to your users database table allows for retrieval in future
                $user->stripe_id = $customer->id;
                $user->save();
            }

            // Create a new checkout session for subscription
            $session = Session::create([
                'customer' => $customer->id,  // associate the customer with the session
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => 'price_1O0msfDSmzjzdNYAHW14i2xL', // Replace with your actual Price ID
                ]],
                'mode' => 'subscription',
                'success_url' => env('SUCCESSFUL_URL'), // your success URL
                'cancel_url' => env('CANCEL_URL'), // your cancel URL
            ]);
            // Return the checkout session URL
            return ApiResponse::success(['url' => $session->url], SuccessMsg::OPERATION_SUCCESSFUL);
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
            // Handle Stripe API exceptions
            return ApiResponse::error(ErrorCode::SERVER_ERROR);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            // Handle general exceptions
            return ApiResponse::error(ErrorCode::SERVER_ERROR);
        }
    }
}
