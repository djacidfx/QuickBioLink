<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;

class StripeController extends Controller
{
    /**
     * Process the payment
     *
     * @param Transaction $transaction
     * @return array
     */
    public static function pay(Transaction $transaction)
    {

        $gateway = PaymentGateway::where('key', 'stripe')->first();

        $planInterval = ($transaction->plan->interval == 1) ? '(Monthly)' : '(Yearly)';
        $paymentName = "Payment for " . $transaction->plan->name . " Plan " . $planInterval;

        $fees = ($transaction->total * $gateway->fees) / 100;
        $totalPrice = round(($transaction->total + $fees), 2);
        $totalPrice = $totalPrice * 100;

        try {
            Stripe::setApiKey($gateway->credentials->secret_key);
            $session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'unit_amount' => $totalPrice,
                        'currency' => settings('currency')->code,
                        'product_data' => [
                            'name' => settings('site_title'),
                            'description' => $paymentName,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'customer_creation' => 'always',
                'customer_email' => $transaction->user->email,
                'payment_method_types' => [
                    'card',
                ],
                'mode' => 'payment',
                'cancel_url' => route('subscription'),
                'success_url' => route('ipn.stripe') . '?session_id={CHECKOUT_SESSION_ID}',
            ]);

            if ($session) {
                $transaction->update(['fees' => $fees, 'payment_id' => $session->id]);

                $data['error'] = false;
                $data['redirect_url'] = $session->url;
                return $data;
            }
        } catch (\Exception$e) {
            $data['error'] = true;
            $data['message'] = $e->getMessage();
            return $data;
        }
    }

    /**
     * Handle the IPN
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function ipn(Request $request)
    {

        try {
            $session_id = $request->session_id;

            $gateway = PaymentGateway::where('key', 'stripe')->first();

            Stripe::setApiKey($gateway->credentials->secret_key);

            $transaction = Transaction::where([
                ['user_id', user_auth_info()->id],
                ['status', Transaction::STATUS_PENDING],
                ['payment_id', $session_id],
            ])->first();

            if (is_null($transaction)) {
                quick_alert_error(lang('Checkout link is expired, please try again.'));
                return redirect()->route('subscription');
            }

            $session = Session::retrieve($session_id);

            if ($session->payment_status == "paid" && $session->status == "complete") {
                $customer = Customer::retrieve($session->customer);

                $update = $transaction->update([
                    'total' => ($transaction->total + $transaction->fees),
                    'payment_gateway_id' => $gateway->id,
                    'payment_id' => $session->id,
                    'payer_id' => $customer->id,
                    'payer_email' => $customer->email,
                    'status' => Transaction::STATUS_PAID,
                ]);
                if ($update) {
                    CheckoutController::processSubscription($transaction);
                    quick_alert_success(lang('Payment successful'));
                }
            } else {
                quick_alert_error(lang('Payment failed, please try again.'));
            }
        } catch (\Exception $exception) {
            quick_alert_error(lang('Payment failed, please try again.'));
        }

        return redirect()->route('subscription');
    }
}
