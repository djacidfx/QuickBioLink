<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Str;

class SubscribeController extends Controller
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
     * @return \Illuminate\Contracts\View\View
     */
    public function mySubscription()
    {
        $user = user_auth_info();
        return view($this->activeTheme . '.user.subscription', compact('user'));
    }

    /**
     * Process the subscription
     *
     * @param Request $request
     * @param $id
     * @param $type
     * @return \Illuminate\Http\RedirectResponse|\never|void
     */
    public function subscribe(Request $request, $id, $type)
    {
        $plan = Plan::findOrFail($id);

        $user = user_auth_info();

        /* Check transaction type is valid */
        if ($type == 'subscribe') {

            abort_if($user->isSubscribed(), 401);
            $type = Transaction::TYPE_SUBSCRIBE;

        } else if ($type == 'renew') {

            abort_if(!$user->isSubscribed() || $user->subscription->isFree() ||
                $user->subscription->plan->id != $plan->id || !$user->subscription->isAboutToExpire(), 401);
            $type = Transaction::TYPE_RENEW;

        } else if ($type == 'upgrade') {

            abort_if(!$user->isSubscribed() || $user->subscription->plan_id == $plan->id, 401);
            $type = Transaction::TYPE_UPGRADE;

        } else if ($type == 'downgrade') {

            abort_if(!$user->isSubscribed() || $user->subscription->plan_id == $plan->id, 401);
            $type = Transaction::TYPE_DOWNGRADE;

        } else {
            return abort(404);
        }

        $tax = ($plan->price * country_tax($user->address->country ?? user_ip_info()->location->country)) / 100;
        $total = ($plan->price + $tax);

        /* Create an unpaid transaction */
        $transaction = Transaction::create([
            'checkout_id' => sha1(Str::random(20) . uniqid()),
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'tax' => $tax,
            'total' => $total,
            'type' => $type,
            'price' => price_format($plan->price),
            'details_before_discount' => [
                'price' => price_format($plan->price),
                'tax' => price_format($tax),
                'total' => price_format($total)
            ],
        ]);

        if ($transaction) {
            return redirect()->route('checkout.index', $transaction->checkout_id);
        }
    }
}
