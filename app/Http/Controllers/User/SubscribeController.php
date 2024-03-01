<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Str;

class SubscribeController extends Controller
{
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    protected function user()
    {
        return user_auth_info();
    }

    public function mySubscription()
    {
        return view($this->activeTheme.'.user.subscription', ['user' => $this->user()]);
    }

    public function subscribe(Request $request, $id, $type)
    {
        $plan = Plan::findOrFail($id);
        $user = user_auth_info();
        abort_if($user->isSubscribed() && $user->subscription->isCancelled(), 401);
        switch ($type) {
            case 'subscribe':
                abort_if($user->isSubscribed(), 401);
                $type = 1;
                break;
            case "renew":
                abort_if(!$user->isSubscribed() || $user->subscription->isFree() ||
                    $user->subscription->plan->id != $plan->id || !$user->subscription->isAboutToExpire(), 401);
                $type = 2;
                break;
            case "upgrade":
                abort_if(!$user->isSubscribed() || $user->subscription->plan_id == $plan->id, 401);
                $type = 3;
                break;
            case "downgrade":
                abort_if(!$user->isSubscribed() || $user->subscription->plan_id == $plan->id || $plan->price > $user->subscription->plan->price && $plan->interval > $user->subscription->plan->interval, 401);
                $type = 4;
                break;
            default:
                return abort(404);
                break;
        }
        $checkoutId = sha1(Str::random(40) . time());
        $tax = ($plan->price * country_tax($user->address->country ?? user_ip_info()->location->country)) / 100;
        $total = ($plan->price + $tax);
        $detailsBeforeDiscount = ['price' => price_format($plan->price), 'tax' => price_format($tax), 'total' => price_format($total)];
        $transaction = Transaction::create([
            'checkout_id' => $checkoutId,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'details_before_discount' => $detailsBeforeDiscount,
            'price' => price_format($plan->price),
            'tax' => $tax,
            'total' => $total,
            'type' => $type,
        ]);
        if ($transaction) {
            return redirect()->route('checkout.index', $transaction->checkout_id);
        }
    }
}
