<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\Transaction;
use App\Notifications\Subscriptions\SubscriptionAboutToExpiredNotification;
use App\Notifications\Subscriptions\SubscriptionDeletedNotification;
use App\Notifications\Subscriptions\SubscriptionExpiredNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class QuickCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quickcms:quick-cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run QuickCMS Cron Job';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /* Delete unpaid transactions */
        $transactions = Transaction::where('created_at', '<=', Carbon::now()->subHour())->whereIn('status', [0, 1])->get();
        if ($transactions->count() > 0) {
            foreach ($transactions as $transaction) {
                $transaction->delete();
            }
        }

        /* Send notification on membership expire */
        if (email_template('subscription_expired')->status) {
            $subscriptions = Subscription::where('expiry_at', '<=', Carbon::now()->subDays(settings('subscription')->expired_reminder))
                ->where('status', Subscription::STATUS_ACTIVE)
                ->notFree()->expiredReminderNotSent()->get();
            if ($subscriptions->count() > 0) {
                foreach ($subscriptions as $subscription) {
                    $subscription->user->notify(new SubscriptionExpiredNotification($subscription));
                    $subscription->update(['expired_reminder' => true]);
                }
            }
        }

        /* Send notification on membership expiring soon */
        if (email_template('subscription_about_expired')->status) {
            $subscriptions = Subscription::notFree()->isAboutToExpire()->aboutToExpireReminderNotSent()->get();
            if ($subscriptions->count() > 0) {
                foreach ($subscriptions as $subscription) {
                    $subscription->user->notify(new SubscriptionAboutToExpiredNotification($subscription));
                    $subscription->update(['about_to_expire_reminder' => true]);
                }
            }
        }

        /* Delete expired memberships */
        $days = settings('subscription')->delete_expired;
        $subscriptions = Subscription::where([['expiry_at', '<', Carbon::now()->subDays($days)], ['status', Subscription::STATUS_ACTIVE]])->notFree()->get();
        if ($subscriptions->count() > 0) {
            foreach ($subscriptions as $subscription) {
                $subscription->user->notify(new SubscriptionDeletedNotification($subscription));
                $subscription->delete();
            }
        }
    }
}
