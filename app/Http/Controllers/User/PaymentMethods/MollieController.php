<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;

class MollieController extends Controller
{

    /**
     * Process the payment
     *
     * @param Transaction $transaction
     * @return array
     */
    public static function pay(Transaction $transaction)
    {

        $gateway = PaymentGateway::where('key', 'mollie')->first();

        $planInterval = ($transaction->plan->interval == 1) ? '(Monthly)' : '(Yearly)';
        $paymentName = "Payment for " . $transaction->plan->name . " Plan " . $planInterval;

        $fees = ($transaction->total * $gateway->fees) / 100;
        $totalPrice = $transaction->total + $fees;
        $totalPrice = number_format((float) $totalPrice, 2);

        config(['mollie.key' => trim($gateway->credentials->api_key)]);

        try {
            $payment = Mollie::api()->payments->create([
                "description" => $paymentName,
                "amount" => [
                    "currency" => settings('currency')->code,
                    "value" => $totalPrice,
                ],
                "redirectUrl" => route('ipn.mollie') . '?checkout_id=' . $transaction->checkout_id,
                "metadata" => [
                    "order_id" => $transaction->id,
                ],
            ]);

            $payment = Mollie::api()->payments()->get($payment->id);

            $transaction->update(['fees' => $fees, 'payment_id' => $payment->id]);

            $data['error'] = false;
            $data['redirect_url'] = $payment->getCheckoutUrl();
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
        $checkoutId = $request->get('checkout_id');
        try {
            $transaction = Transaction::where([
                ['user_id', user_auth_info()->id],
                ['checkout_id', $checkoutId],
                ['status', Transaction::STATUS_PENDING],
                ['payment_id', '!=', null],
            ])->first();

            if (is_null($transaction)) {
                quick_alert_error(lang('Checkout link is expired, please try again.'));
                return redirect()->route('subscription');
            }

            $gateway = PaymentGateway::where('key', 'mollie')->first();

            config(['mollie.key' => trim($gateway->credentials->api_key)]);
            $payment = Mollie::api()->payments()->get($transaction->payment_id);

            if ($payment->metadata->order_id != $transaction->id) {
                quick_alert_error(lang('Checkout link is expired, please try again.'));
                return redirect()->route('subscription');
            }

            if ($payment->status == "paid") {

                $update = $transaction->update([
                    'total' => ($transaction->total + $transaction->fees),
                    'payment_gateway_id' => $gateway->id,
                    'payment_id' => $payment->id,
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
