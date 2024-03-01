<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\PaymentGateway;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    public function index($checkout_id)
    {
        $transaction = Transaction::where([['checkout_id', $checkout_id], ['user_id', user_auth_info()->id]])->unpaid()->firstOrFail();
        $paymentGateways = PaymentGateway::active()->get();
        return view($this->activeTheme.'.user.checkout', [
            'user' => user_auth_info(),
            'transaction' => $transaction,
            'paymentGateways' => $paymentGateways,
        ]);
    }

    public function applyCoupon(Request $request, $checkout_id)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => ['required', 'string', 'max:20'],
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back()->withInput();
        }
        $transaction = Transaction::where([['checkout_id', $checkout_id], ['user_id', user_auth_info()->id], ['coupon_id', null], ['total', '!=', 0]])->unpaid()->firstOrFail();
        $coupon = Coupon::validCode($request->coupon_code)->validForPlan($transaction->plan->id)->first();
        if (!$coupon) {
            quick_alert_error(lang('Invalid or expired coupon code', 'checkout'));
            return back()->withInput();
        }
        if ($coupon->action_type != 0) {
            if ($transaction->type != $coupon->action_type) {
                quick_alert_error(lang('Invalid or expired coupon code', 'checkout'));
                return back()->withInput();
            }
        }
        $couponTransactionsCount = Transaction::where([['coupon_id', $coupon->id], ['user_id', user_auth_info()->id]])->whereIn('status', [0, 2])->count();
        if ($couponTransactionsCount >= $coupon->limit) {
            quick_alert_error(lang('You have exceeded the usage limit for this coupon', 'checkout'));
            return back()->withInput();
        }
        $planPriceAfterDiscount = ($transaction->price - ($transaction->price * $coupon->percentage) / 100);
        $taxPriceAfterDiscount = ($planPriceAfterDiscount * country_tax(user_auth_info()->address->country ?? user_ip_info()->location->country)) / 100;
        $totalPriceAfterDiscount = ($planPriceAfterDiscount + $taxPriceAfterDiscount);
        $detailsAfterDiscount = [
            'price' => price_format($planPriceAfterDiscount),
            'tax' => price_format($taxPriceAfterDiscount),
            'total' => price_format($totalPriceAfterDiscount),
        ];
        $updateTransaction = $transaction->update([
            'coupon_id' => $coupon->id,
            'details_after_discount' => $detailsAfterDiscount,
            'price' => $planPriceAfterDiscount,
            'tax' => $taxPriceAfterDiscount,
            'total' => $totalPriceAfterDiscount,
        ]);
        if ($updateTransaction) {
            quick_alert_success(lang('Coupon has been applied successfully', 'checkout'));
            return back();
        }
    }

    public function removeCoupon(Request $request, $checkout_id)
    {
        $transaction = Transaction::where([['checkout_id', $checkout_id], ['user_id', user_auth_info()->id], ['coupon_id', '!=', null]])->unpaid()->firstOrFail();
        $updateTransaction = $transaction->update([
            'coupon_id' => null,
            'details_after_discount' => null,
            'price' => $transaction->details_before_discount->price,
            'tax' => $transaction->details_before_discount->tax,
            'total' => $transaction->details_before_discount->total,
        ]);
        if ($updateTransaction) {
            return back();
        }
    }

    public function process(Request $request, $checkout_id)
    {
        $validator = Validator::make($request->all(), [
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:150'],
            'state' => ['required', 'string', 'max:150'],
            'zip' => ['required', 'string', 'max:100'],
            'country' => ['required', 'integer', 'exists:countries,id'],
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back();
        }
        $transaction = Transaction::where([['checkout_id', $checkout_id], ['user_id', user_auth_info()->id]])->unpaid()->firstOrFail();
        if ($transaction->coupon_id) {
            if (!$transaction->coupon || $transaction->coupon->isExpiry()) {
                quick_alert_error(lang('Invalid or expired coupon code', 'checkout'));
                return back()->withInput();
            }
        }
        if ($transaction->total != 0) {
            $paymentGateway = PaymentGateway::where('id', $request->payment_method)->active()->first();
            if (!$paymentGateway) {
                quick_alert_error(lang('Selected payment method is not active', 'checkout'));
                return back();
            }
        }
        $country = Country::find($request->country);
        $address = [
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $country->name,
        ];
        $user = Auth::user();
        $user->update(['address' => $address]);

        if ($transaction->total == 0) {
            $transaction->update(['status' => 2]);
            $this->updateSubscription($transaction);
            quick_alert_success(lang('Subscribed Successfully', 'checkout'));
            return redirect()->route('subscription');
        }

        $paymentHandler = 'App\Http\Controllers\User\Gateways\\'.ucfirst($paymentGateway->key).'Controller';

        $paymentData = $paymentHandler::process($transaction);
        $paymentData = json_decode($paymentData);
        if ($paymentData->error == true) {
            quick_alert_error($paymentData->msg);
            return back();
        }
        $updateTransaction = $transaction->update(['status' => 1,'billing_address' => $address]);
        if ($updateTransaction) {
            if (isset($paymentData->redirectUrl)) {
                return redirect($paymentData->redirectUrl);
            }
            return view($paymentData->view, [
                'details' => $paymentData->details,
                'trx' => $paymentData->trx,
            ]);
        }
    }

    public static function updateSubscription($transaction)
    {
        if ($transaction->status != 2) {
            throw new Exception(lang('Incomplete payment', 'checkout'));
        }
        if ($transaction->type == 1) {
            $expiry_at = ($transaction->plan->interval == 1) ? Carbon::now()->addMonth() : Carbon::now()->addYear();
            $subscription = new Subscription();
            $subscription->user_id = $transaction->user_id;
            $subscription->plan_id = $transaction->plan_id;
            $subscription->expiry_at = $expiry_at;
            $subscription->plan_settings = $transaction->plan->settings;
            $subscription->save();
        }
        if ($transaction->type == 2) {
            $subscription = $transaction->user->subscription;
            if ($transaction->plan->interval == 1) {
                if ($subscription->isExpired()) {
                    $expiry_at = Carbon::now()->addMonth();
                } else {
                    $expiry_at = Carbon::parse($subscription->expiry_at)->addMonth();
                }
            } else {
                if ($subscription->isExpired()) {
                    $expiry_at = Carbon::now()->addYear();
                } else {
                    $expiry_at = Carbon::parse($subscription->expiry_at)->addYear();
                }
            }
            $subscription->expiry_at = $expiry_at;
            $subscription->plan_settings = $transaction->plan->settings;
            $subscription->about_to_expire_reminder = false;
            $subscription->expired_reminder = false;
            $subscription->update();
        }
        if ($transaction->type == 3 || $transaction->type == 4) {
            $subscription = $transaction->user->subscription;
            $expiry_at = ($transaction->plan->interval == 1) ? Carbon::now()->addMonth() : Carbon::now()->addYear();
            $subscription->plan_id = $transaction->plan_id;
            $subscription->expiry_at = $expiry_at;
            $subscription->plan_settings = $transaction->plan->settings;
            $subscription->about_to_expire_reminder = false;
            $subscription->expired_reminder = false;
            $subscription->update();
        }
    }
}
