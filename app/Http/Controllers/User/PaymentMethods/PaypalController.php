<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{

    /**
     * Get paypal provider
     *
     * @return PayPalClient
     */
    public static function getPaypalProvider()
    {
        $gateway = PaymentGateway::where('key', 'paypal')->first();

        if($gateway->test_mode){
            $config = [
                'mode'    => 'sandbox',
                'sandbox' => [
                    'client_id'         => $gateway->credentials->client_id,
                    'client_secret'     => $gateway->credentials->client_secret,
                    'app_id'            => 'APP-80W284485P519543T',
                ],

                'payment_action' => 'Sale',
                'currency'       => settings('currency')->code,
                'notify_url'     => '',
                'validate_ssl'   => false,
                'locale' => get_lang()
            ];
        }else{
            $config = [
                'mode'    => 'live',
                'live' => [
                    'client_id'         => $gateway->credentials->client_id,
                    'client_secret'     => $gateway->credentials->client_secret,
                    'app_id'            => $gateway->credentials->app_id,
                ],

                'payment_action' => 'Sale',
                'currency'       => settings('currency')->code,
                'notify_url'     => '',
                'validate_ssl'   => true,
                'locale' => get_lang()
            ];
        }

        $provider = new PayPalClient($config);
        $provider->getAccessToken();
        return $provider;
    }

    /**
     * Process the payment
     *
     * @param Transaction $transaction
     * @return array
     */
    public static function pay(Transaction $transaction)
    {

        $gateway = PaymentGateway::where('key', 'paypal')->first();

        $planInterval = ($transaction->plan->interval == 1) ? '(Monthly)' : '(Yearly)';

        $paymentName = "Payment for " . $transaction->plan->name . " Plan " . $planInterval;
        $gatewayFees = ($transaction->total * $gateway->fees) / 100;
        $priceIncludeFees = ($transaction->total + $gatewayFees);

        try {
            $provider = self::getPaypalProvider();
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                'application_context' => [
                    'brand_name' => settings('site_title'),
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                    "return_url" => route('ipn.paypal'),
                    "cancel_url" => route('subscription')
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => settings('currency')->code,
                            "value" => number_format((float) $priceIncludeFees, 2)
                        ]
                    ]
                ]
            ]);

            $redirect_url = '';

            if (isset($response['id']) && $response['id'] != null) {
                // redirect to approve href
                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {
                        $redirect_url = $links['href'];
                        break;
                    }
                }

                $transaction->update(['fees' => $gatewayFees, 'payment_id' => $response['id']]);

                $data['error'] = false;
                $data['redirect_url'] = $redirect_url;
                return $data;

            } else {
                $data['error'] = true;
                $data['message'] = !empty($response['error']['message'])
                    ? lang('Payment failed') .' : '. $response['error']['message']
                    : lang('Payment failed, check the credentials.');

                error_log($response);
                return $data;
            }

        } catch (\Exception $e) {
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
            $provider = self::getPaypalProvider();

            $response = $provider->capturePaymentOrder($request['token']);

            $transaction = Transaction::where([
                ['user_id', user_auth_info()->id],
                ['payment_id', $request['token']],
                ['status', Transaction::STATUS_PENDING],
            ])->first();

            if (is_null($transaction)) {
                quick_alert_error(lang('Checkout link is expired, please try again.'));
                return redirect()->route('subscription');
            }

            $gateway = PaymentGateway::where('key', 'paypal')->first();

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {

                $update = $transaction->update([
                    'total' => ($transaction->total + $transaction->fees),
                    'payment_gateway_id' => $gateway->id,
                    'payment_id' => $request['token'],
                    'payer_id' => $response['payer']['payer_id'],
                    'payer_email' => $response['payer']['email_address'],
                    'status' => Transaction::STATUS_PAID,
                ]);
                if ($update) {
                    CheckoutController::processSubscription($transaction);
                    quick_alert_success(lang('Payment successful'));
                }
            } else {
                quick_alert_error(lang('Payment failed, please try again.'));
            }

        } catch (\Exception $e) {
            quick_alert_error(lang('Payment failed, please try again.'));
        }

        return redirect()->route('subscription');
    }
}
