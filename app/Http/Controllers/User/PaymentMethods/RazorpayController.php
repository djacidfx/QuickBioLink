<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class RazorpayController extends Controller
{

    /**
     * Process the payment
     *
     * @param Transaction $transaction
     * @return array
     */
    public static function pay(Transaction $transaction)
    {

        $gateway = PaymentGateway::where('key', 'razorpay')->first();

        $planInterval = ($transaction->plan->interval == 1) ? '(Monthly)' : '(Yearly)';
        $paymentName = "Payment for " . $transaction->plan->name . " Plan " . $planInterval;

        $fees = ($transaction->total * $gateway->fees) / 100;
        $totalPrice = round(($transaction->total + $fees), 2);
        $totalPrice = $totalPrice * 100;

        try {
            $api = new Api($gateway->credentials->key_id, $gateway->credentials->key_secret);
            $order = $api->order->create([
                'receipt' => (string)$transaction->id,
                'amount' => $totalPrice,
                'currency' => settings('currency')->code,
                'payment_capture' => '0',
            ]);

            $data['error'] = false;
            $data['transaction'] = $transaction;
            $data['details'] = [
                'key' => $gateway->credentials->key_id,
                'amount' => $totalPrice,
                'order_id' => $order['id'],
                'description' => $paymentName,
                'name' => settings('site_title'),
                'currency' => settings('currency')->code,
                'prefill.name' => user_auth_info()->name,
                'prefill.email' => user_auth_info()->email,
                'theme.color' => settings('colors')->primary_color,
                'buttontext' => lang('Pay Now'),
                'image' => '',
            ];
            $data['view'] = active_theme()."user.gateways." . $gateway->key;

            $transaction->update(['fees' => $fees, 'payment_id' => $order['id']]);

            return $data;
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

            $checkoutId = $request->checkout_id;
            $paymentId = $request->razorpay_order_id;

            $transaction = Transaction::where([
                ['user_id', user_auth_info()->id],
                ['checkout_id', $checkoutId],
                ['payment_id', $paymentId],
                ['status', Transaction::STATUS_PENDING],
            ])->first();

            if (is_null($transaction)) {
                quick_alert_error(lang('Checkout link is expired, please try again.'));
                return redirect()->route('subscription');
            }

            $gateway = PaymentGateway::where('key', 'razorpay')->first();

            $signature = hash_hmac('sha256', $request->razorpay_order_id . "|" . $request->razorpay_payment_id, $gateway->credentials->key_secret);

            if ($signature == $request->razorpay_signature) {

                $update = $transaction->update([
                    'total' => ($transaction->total + $transaction->fees),
                    'payment_gateway_id' => $gateway->id,
                    'payment_id' => $request->razorpay_payment_id,
                    'status' => Transaction::STATUS_PAID,
                ]);
                if ($update) {
                    CheckoutController::processSubscription($transaction);
                    quick_alert_success(lang('Payment successful'));
                }
            } else {
                quick_alert_error(lang('Payment failed, please try again.'));
            }
        } catch (\Exception$e) {
            quick_alert_error(lang('Payment failed, please try again.'));
        }

        return redirect()->route('subscription');
    }
}
