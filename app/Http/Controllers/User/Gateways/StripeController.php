<?php

namespace App\Http\Controllers\User\Gateways;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;

class StripeController extends Controller
{
    public static function process($trx)
    {
        if ($trx->status != 0) {
            $data['error'] = true;
            $data['msg'] = lang('Invalid or expired transaction', 'checkout');
            return json_encode($data);
        }

        $paymentGateway = PaymentGateway::where('key', 'stripe')->first();

        $planInterval = ($trx->plan->interval == 1) ? '(Monthly)' : '(Yearly)';
        $paymentName = "Payment for subscription " . $trx->plan->name . " Plan " . $planInterval;
        $gatewayFees = ($trx->total * $paymentGateway->fees) / 100;
        $totalPrice = round(($trx->total + $gatewayFees), 2);
        $priceIncludeFees = str_replace('.', '', ($totalPrice * 100));
        $paymentDeatails = [
            'customer_creation' => 'always',
            'customer_email' => $trx->user->email,
            'payment_method_types' => [
                'card',
            ],
            'line_items' => [[
                'price_data' => [
                    'unit_amount' => $priceIncludeFees,
                    'currency' => settings('currency')->code,
                    'product_data' => [
                        'name' => settings('site_title'),
                        'description' => $paymentName,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'cancel_url' => route('subscription'),
            'success_url' => route('ipn.stripe') . '?session_id={CHECKOUT_SESSION_ID}',
        ];
        try {
            Stripe::setApiKey($paymentGateway->credentials->secret_key);
            $session = Session::create($paymentDeatails);
            if ($session) {
                $trx->update(['fees' => $gatewayFees, 'payment_id' => $session->id]);
                $data['error'] = false;
                $data['redirectUrl'] = $session->url;
                return json_encode($data);
            }
        } catch (\Exception$e) {
            $data['error'] = true;
            $data['msg'] = $e->getMessage();
            return json_encode($data);
        }
    }

    public function ipn(Request $request)
    {
        $session_id = $request->session_id;

        $paymentGateway = PaymentGateway::where('key', 'stripe')->first();

        try {
            Stripe::setApiKey($paymentGateway->credentials->secret_key);
            $trx = \App\Models\Transaction::where([['user_id', user_auth_info()->id], ['payment_id', $session_id]])->pending()->first();
            if (is_null($trx)) {
                quick_alert_error(lang('Invalid or expired transaction', 'checkout'));
                return redirect()->route('subscription');
            }
            $session = Session::retrieve($session_id);
            if ($session->payment_status == "paid" && $session->status == "complete") {
                $customer = Customer::retrieve($session->customer);
                $total = ($trx->total + $trx->fees);
                $payment_gateway_id = $paymentGateway->id;
                $payment_id = $session->id;
                $payer_id = $customer->id;
                $payer_email = $customer->email;
                $updateTrx = $trx->update([
                    'total' => $total,
                    'payment_gateway_id' => $payment_gateway_id,
                    'payment_id' => $payment_id,
                    'payer_id' => $payer_id,
                    'payer_email' => $payer_email,
                    'status' => \App\Models\Transaction::STATUS_PAID,
                ]);
                if ($updateTrx) {
                    CheckoutController::updateSubscription($trx);
                    quick_alert_success(lang('Payment made successfully', 'checkout'));
                    return redirect()->route('subscription');
                }
            } else {
                quick_alert_error(lang('Payment failed', 'checkout'));
                return redirect()->route('subscription');
            }
        } catch (\Exception $exception) {
            quick_alert_error(lang('Payment failed', 'checkout') . ' '. $exception->getMessage());
            return redirect()->route('subscription');
        }
    }
}
