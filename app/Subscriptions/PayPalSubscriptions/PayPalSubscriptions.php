<?php

namespace App\Subscriptions\PayPalSubscriptions;


use App\Plan;
use Exception;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalSubscriptions
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new PayPalClient;
        $this->provider->setApiCredentials(config('paypal'));
    }

    // Methods for creating, canceling, pausing, resuming, and updating subscriptions
    // Implement the methods defined in the Subscriptions interface
    public function create(int $plan_id, int $coupon_user_id, string $method, float $amount = 0)
    {

        // Set up the shipping address
        $address = [
            "address_line_1" => "2211 N First Street",
            "address_line_2" => "Building 17",
            "admin_area_2" => "San Jose",
            "admin_area_1" => "CA",
            "postal_code" => "95131",
            "country_code" => "US",
        ];

        // $plan_data = json_decode('{
        //     "product_id": "PROD-XXCD1234QWER65782",
        //     "name": "Video Streaming Service Plan",
        //     "description": "Video Streaming Service basic plan",
        //     "status": "ACTIVE",
        //     "billing_cycles": [
        //       {
        //         "frequency": {
        //           "interval_unit": "MONTH",
        //           "interval_count": 1
        //         },
        //         "tenure_type": "TRIAL",
        //         "sequence": 1,
        //         "total_cycles": 2,
        //         "pricing_scheme": {
        //           "fixed_price": {
        //             "value": "3",
        //             "currency_code": "USD"
        //           }
        //         }
        //       },
        //       {
        //         "frequency": {
        //           "interval_unit": "MONTH",
        //           "interval_count": 1
        //         },
        //         "tenure_type": "TRIAL",
        //         "sequence": 2,
        //         "total_cycles": 3,
        //         "pricing_scheme": {
        //           "fixed_price": {
        //             "value": "6",
        //             "currency_code": "USD"
        //           }
        //         }
        //       },
        //       {
        //         "frequency": {
        //           "interval_unit": "MONTH",
        //           "interval_count": 1
        //         },
        //         "tenure_type": "REGULAR",
        //         "sequence": 3,
        //         "total_cycles": 12,
        //         "pricing_scheme": {
        //           "fixed_price": {
        //             "value": "10",
        //             "currency_code": "USD"
        //           }
        //         }
        //       }
        //     ],
        //     "payment_preferences": {
        //       "auto_bill_outstanding": true,
        //       "setup_fee": {
        //         "value": "10",
        //         "currency_code": "USD"
        //       },
        //       "setup_fee_failure_action": "CONTINUE",
        //       "payment_failure_threshold": 3
        //     },
        //     "taxes": {
        //       "percentage": "10",
        //       "inclusive": false
        //     }
        //   }', true);

        // $paypalPlanId = $this->provider->createPlan($plan_data, $plan_id)->plan_id;
        // Retrieve the PayPal plan ID from the selected plan
        $paypalPlanId = Plan::where('id', $plan_id)->first()->id;
        // Prepare the data for subscription creation
        // example data
        $data = [
            'plan_id' => $paypalPlanId,
            'quantity' => '1',
            'shipping_amount' => [
                'currency_code' => 'EUR',
                'value' => 0.00,
            ],
            'subscriber' => [
                'name' => [
                    'given_name' => auth()->user()->name,
                    'surname' => '',
                ],
                'email_address' => auth()->user()->email,
                'shipping_address' => [
                    'name' => [
                        'full_name' => auth()->user()->id . '-' . $coupon_user_id,
                    ],
                    'address' => $address,
                ],
            ],
            'application_context' => [
                'brand_name' => env('PAYPAL_PRODUCT_ID'),
                'locale' => 'en-US',
                'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                'user_action' => 'SUBSCRIBE_NOW',
                'payment_method' => [
                    'payer_selected' => 'PAYPAL',
                    'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                ],
                'return_url' => route('payments.paypal.success', [$plan_id]),
                'cancel_url' => route('payments.cancel'),
            ],
        ];

        // Include additional data for subscription with a coupon
        if ($coupon_user_id != 0) {
            $data['plan'] = [
                'billing_cycles' => [
                    [
                        'sequence' => 1,
                        'total_cycles' => 1,
                        'pricing_scheme' => [
                            'fixed_price' => [
                                'value' => $amount, // discounted amount
                                'currency_code' => 'USD',
                            ],
                        ],
                    ],
                ],
            ];
        }

        // Send the request to create the subscription
        $response = $this->provider->createSubscription($data);

        if (isset($response['id']) && $response['id'] != null) {
            // Find the approval URL
            $approvalUrl = null;
            foreach ($response['links'] as $link) {
                if ($link['rel'] == 'approve') {
                    $approvalUrl = $link['href'];
                    break;
                }
            }

            // Return the subscription ID and approval URL
            return [
                'subscription_id' => $response['id'],
                'approval_url' => $approvalUrl,
            ];
        } else {
            // Return an error message
            return [
                'error' => $response['message'] ?? 'Something went wrong.',
            ];
        }
    }

    public function cancel(string $subscription_id = null)
    {
        // if (is_null($subscription_id)) {
        //     $subscription = Subscription::where('user_id', auth()->user()->id)->first();
        //     $reason = 'no longer using';
        // } else {
        //     $subscription = Subscription::where('subscription_id', $subscription_id)->first();
        //     $reason = 'new subscription';
        // }
        // $subscriptionId = $subscription->subscription_id;
        // try {
        //     $response = $this->provider->cancelSubscription($subscriptionId, $reason);
        //     return true;
        // } catch (Exception $e) {
        //     $error = "Something went wrong." . $e->getMessage();
        //     return false;
        // }
    }
    public function pause()
    {
        // Implementation goes here
    }

    public function resume()
    {
        // Implementation goes here
    }
}
