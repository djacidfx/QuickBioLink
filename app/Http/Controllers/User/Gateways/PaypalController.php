<?php

namespace App\Http\Controllers\User\Gateways;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{

    public static function getPaypalProvider()
    {
        $paymentGateway = PaymentGateway::where('key', 'paypal')->first();

        if($paymentGateway->test_mode){
            $config = [
                'mode'    => 'sandbox',
                'sandbox' => [
                    'client_id'         => $paymentGateway->credentials->client_id,
                    'client_secret'     => $paymentGateway->credentials->client_secret,
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
                    'client_id'         => $paymentGateway->credentials->client_id,
                    'client_secret'     => $paymentGateway->credentials->client_secret,
                    'app_id'            => $paymentGateway->credentials->app_id,
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

    public static function process($trx)
    {
        if ($trx->status != 0) {
            $data['error'] = true;
            $data['msg'] = lang('Invalid or expired transaction', 'checkout');
            return json_encode($data);
        }

        $paymentGateway = PaymentGateway::where('key', 'paypal')->first();

        $planInterval = ($trx->plan->interval == 1) ? '(Monthly)' : '(Yearly)';

        $paymentName = "Payment for subscription " . $trx->plan->name . " Plan " . $planInterval;
        $gatewayFees = ($trx->total * $paymentGateway->fees) / 100;
        $priceIncludeFees = ($trx->total + $gatewayFees);


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

                $trx->update(['fees' => $gatewayFees, 'payment_id' => $response['id']]);

                $data['error'] = false;
                $data['redirectUrl'] = $redirect_url;
                return json_encode($data);
            } else {
                $data['error'] = true;
                $data['msg'] = !empty($response['error']['message'])
                    ? lang('Payment failed', 'checkout') .' : '. $response['error']['message']
                    : lang('Payment failed, check the credentials.', 'checkout');
                error_log($response);
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
        try {
            $provider = self::getPaypalProvider();
            $response = $provider->capturePaymentOrder($request['token']);

            $trx = \App\Models\Transaction::where([['user_id', user_auth_info()->id], ['payment_id', $request['token']]])->pending()->first();
            if (is_null($trx)) {
                quick_alert_error(lang('Invalid or expired transaction', 'checkout'));
                return redirect()->route('subscription');
            }

            $paymentGateway = PaymentGateway::where('key', 'paypal')->first();

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                $payment_gateway_id = $paymentGateway->id;
                $payment_id = $request['token'];
                $payer_id = $response['payer']['payer_id'];
                $payer_email = $response['payer']['email_address'];
                $total = ($trx->total + $trx->fees);
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

        } catch (\Exception $e) {
            quick_alert_error(lang('Payment failed', 'checkout'));
            return redirect()->route('subscription');
        }
    }
}
