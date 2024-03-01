<?php

namespace App\Http\Methods;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SubscriptionManager
{
    public static function subscription($user)
    {
        $subscription = self::users($user);

        return array_to_object($subscription);
    }

    private static function users($user)
    {
        if ($user && $user->isSubscribed()) {
            if($user->subscription->isActive()) {
                $subscription['is_subscribed'] = true;
                $subscription['plan'] = $user->subscription->plan;
                $subscription['plan']['settings'] = $user->subscription->plan_settings;
                return $subscription;
            }
        }
        return self::free();
    }

    private static function free()
    {
        $subscription = null;
        $plan = Plan::free()->first();
        if ($plan) {
            $subscription['is_subscribed'] = true;
            $subscription['plan'] = $plan;
        }
        return $subscription;
    }

}
