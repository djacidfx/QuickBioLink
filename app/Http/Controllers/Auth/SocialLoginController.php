<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Exception;

class SocialLoginController extends Controller
{
    /**
     * Redirect to social provider
     */
    public function redirect($provider)
    {
        abort_if(!@settings($provider.'_login')->status, 404);
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the callback
     */
    public function callback($provider)
    {
        try {

            abort_if(!@settings($provider.'_login')->status, 404);

            $user = Socialite::driver($provider)->user();

            $existing_user = User::where('oauth_id', $user->getId())
                ->orWhere('email', $user->getEmail())->first();

            if ($existing_user) {
                update_user_logs($existing_user);
                Auth::login($existing_user);

                return redirect(RouteServiceProvider::USER);

            } else {
                if (!settings('enable_user_registration')) {
                    quick_alert_error(lang('Registration is currently disabled.'));
                    return redirect()->route('login');
                }

                $name = explode(' ', $user->getName());

                $ipInfo = user_ip_info();

                $username = explode('@', $user->getEmail());
                $username = SlugService::createSlug(User::class, 'username', $username[0]);
                $username = str_replace('-','_', $username);

                $new_user = User::create([
                    'name' => $user->getName(),
                    'firstname' => $name[0] ?? null,
                    'lastname' => $name[1] ?? null,
                    'username' => $username,
                    'email' => $user->getEmail(),
                    'address' => ['address' => '', 'city' => '', 'state' => '', 'zip' => '', 'country' => $ipInfo->location->country],
                    'avatar' => 'default.png',
                    'password' => Hash::make(Str::random(10)),
                    'oauth_id'=> $user->getId(),
                    'oauth_provider'=> $provider,
                ]);
                $new_user->forceFill(['email_verified_at' => Carbon::now()])->save();

                event(new Registered($new_user));

                $title = $user->name . ' ' . lang('has registered');
                $link = route('admin.users.edit', $new_user->id);
                admin_notify($title, 'new_user', $link);

                update_user_logs($new_user);
                Auth::login($new_user);

                return redirect(RouteServiceProvider::USER);
            }

        } catch (Exception $e) {
            quick_alert_error($e->getMessage());
            return redirect()->route('login');
        }
    }

}
