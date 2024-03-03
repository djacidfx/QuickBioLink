<?php

namespace App\Http\Methods;

use App\Models\Plan;

class SubscriptionManager
{
    /**
     * Get subscription details
     *
     * @param $user
     * @return mixed
     */
    public static function subscription($user)
    {
        $subscription = self::users($user);

        return array_to_object($subscription);
    }

    /**
     * Get user's subscription details
     *
     * @param $user
     * @return array
     */
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

    /**
     * Get free plan details
     *
     * @return array
     */
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
