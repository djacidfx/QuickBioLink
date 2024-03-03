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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    /**
     * Display the page
     *
     * @param $checkout_id
     * @return \Illuminate\Contracts\View\View
     */
    public function index($checkout_id)
    {
        $user = user_auth_info();

        $transaction = Transaction::where([
            ['user_id', $user->id],
            ['checkout_id', $checkout_id],
            ['status', Transaction::STATUS_UNPAID],
        ])->firstOrFail();

        $paymentGateways = PaymentGateway::where('status', 1)->get();

        return view($this->activeTheme . '.user.checkout', compact('user', 'transaction', 'paymentGateways'));
    }

    /**
     * Process the payment
     *
     * @param Request $request
     * @param $checkout_id
     */
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

        $transaction = Transaction::where([
            ['user_id', user_auth_info()->id],
            ['checkout_id', $checkout_id],
            ['status', Transaction::STATUS_UNPAID],
        ])->firstOrFail();

        /* Check coupon expiry time */
        if ($transaction->coupon_id) {
            if (!$transaction->coupon || $transaction->coupon->isExpiry()) {
                quick_alert_error(lang('Coupon code is expired or invalid.'));
                return back()->withInput();
            }
        }

        if ($transaction->total != 0) {
            $paymentGateway = PaymentGateway::where('id', $request->payment_method)->where('status', 1)->first();
            if (!$paymentGateway) {
                quick_alert_error(lang('Unexpected error'));
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

        /* Process free subscription */
        if ($transaction->total == 0) {
            $transaction->update(['status' => Transaction::STATUS_PAID]);
            $this->processSubscription($transaction);
            quick_alert_success(lang('Subscribed Successfully'));
            return redirect()->route('subscription');
        }

        $paymentController = 'App\Http\Controllers\User\PaymentMethods\\' . ucfirst($paymentGateway->key) . 'Controller';

        $result = $paymentController::pay($transaction);

        if ($result['error'] == true) {
            quick_alert_error($result['message']);
            return back();
        }

        $update = $transaction->update(['status' => Transaction::STATUS_PENDING, 'billing_address' => $address]);
        if ($update) {
            if (isset($result['redirect_url'])) {
                /* redirect to payment gateway page */
                return redirect($result['redirect_url']);
            }
            /* display payment gateway form */
            $details = $result['details'];
            $transaction = $result['transaction'];
            return view($result['view'], compact('details', 'transaction'));
        }
    }

    /**
     * Update Subscription data
     *
     * @param Transaction $transaction
     */
    public static function processSubscription(Transaction $transaction)
    {

        /* Subscribe the user */
        if ($transaction->type == Transaction::TYPE_SUBSCRIBE) {
            $expiry_at = ($transaction->plan->interval == 1) ? Carbon::now()->addMonth() : Carbon::now()->addYear();

            $subscription = new Subscription();
            $subscription->user_id = $transaction->user_id;
            $subscription->plan_id = $transaction->plan_id;
            $subscription->plan_settings = $transaction->plan->settings;
            $subscription->expiry_at = $expiry_at;
            $subscription->save();
        }

        /* Renew the subscription */
        if ($transaction->type == Transaction::TYPE_RENEW) {

            if ($transaction->plan->interval == 1) {
                if ($subscription->isExpired()) {
                    $expiry_at = Carbon::now()->addMonth();
                } else {
                    /* Extend the subscription if not expired */
                    $expiry_at = Carbon::parse($subscription->expiry_at)->addMonth();
                }
            } else {
                if ($subscription->isExpired()) {
                    $expiry_at = Carbon::now()->addYear();
                } else {
                    /* Extend the subscription if not expired */
                    $expiry_at = Carbon::parse($subscription->expiry_at)->addYear();
                }
            }

            $subscription = $transaction->user->subscription;
            $subscription->plan_settings = $transaction->plan->settings;
            $subscription->about_to_expire_reminder = false;
            $subscription->expired_reminder = false;
            $subscription->expiry_at = $expiry_at;
            $subscription->update();
        }

        /* Upgrade or downgrade the subscription */
        if ($transaction->type == Transaction::TYPE_UPGRADE || $transaction->type == Transaction::TYPE_DOWNGRADE) {

            $expiry_at = ($transaction->plan->interval == 1) ? Carbon::now()->addMonth() : Carbon::now()->addYear();

            $subscription = $transaction->user->subscription;
            $subscription->plan_id = $transaction->plan_id;
            $subscription->plan_settings = $transaction->plan->settings;
            $subscription->about_to_expire_reminder = false;
            $subscription->expired_reminder = false;
            $subscription->expiry_at = $expiry_at;
            $subscription->update();
        }
    }

    /**
     * Apply coupon code
     *
     * @param Request $request
     * @param $checkout_id
     * @return \Illuminate\Http\RedirectResponse|void
     */
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

        $transaction = Transaction::where([
            ['user_id', user_auth_info()->id],
            ['checkout_id', $checkout_id],
            ['coupon_id', null],
            ['total', '!=', 0],
            ['status', Transaction::STATUS_UNPAID],
        ])->firstOrFail();

        $planId = $transaction->plan->id;

        $coupon = Coupon::where('code', $request->coupon_code)
            /* check coupon is not expired */
            ->where(function ($query) {
                $query->where('expiry_at', '>', Carbon::now());
            })
            /* check coupon assigned to this plan */
            ->where(function ($query) use ($planId) {
                $query
                    ->where('plan_id', $planId)
                    ->orWhereNull('plan_id');
            })
            ->first();

        if (!$coupon) {
            quick_alert_error(lang('Coupon code is expired or invalid.'));
            return back()->withInput();
        }

        if ($coupon->action_type != 0) {
            if ($transaction->type != $coupon->action_type) {
                quick_alert_error(lang('Coupon code is expired or invalid.'));
                return back()->withInput();
            }
        }

        $couponUsesCount = Transaction::where([
            ['coupon_id', $coupon->id],
            ['user_id', user_auth_info()->id]])
            ->whereIn('status', [Transaction::STATUS_UNPAID, Transaction::STATUS_PAID])->count();

        if ($couponUsesCount >= $coupon->limit) {
            quick_alert_error(lang('Coupon code usage limit exceeded.'));
            return back()->withInput();
        }

        $planAfterDiscount = ($transaction->price - ($transaction->price * $coupon->percentage) / 100);

        $taxAfterDiscount = ($planAfterDiscount * country_tax(user_auth_info()->address->country ?? user_ip_info()->location->country)) / 100;

        $totalPrice = ($planAfterDiscount + $taxAfterDiscount);

        $update = $transaction->update([
            'coupon_id' => $coupon->id,
            'price' => $planAfterDiscount,
            'tax' => $taxAfterDiscount,
            'total' => $totalPrice,
            'details_after_discount' => [
                'price' => price_format($planAfterDiscount),
                'tax' => price_format($taxAfterDiscount),
                'total' => price_format($totalPrice),
            ],
        ]);
        if ($update) {
            quick_alert_success(lang('Coupon applied successfully'));
            return back();
        }
    }

    /**
     * Remove coupon code
     *
     * @param Request $request
     * @param $checkout_id
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function removeCoupon(Request $request, $checkout_id)
    {
        $transaction = Transaction::where([
            ['user_id', user_auth_info()->id],
            ['checkout_id', $checkout_id],
            ['coupon_id', '!=', null],
            ['status', Transaction::STATUS_UNPAID],
        ])->firstOrFail();

        /* remove the coupon and reset the details */
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
}
