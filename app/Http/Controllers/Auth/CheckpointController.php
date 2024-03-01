<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Validator;

class CheckpointController extends Controller
{
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    public function show2FaVerifyForm()
    {
        if (user_auth_info()->google2fa_status) {
            if (Session::has('2fa')) {
                return redirect(RouteServiceProvider::USER);
            }
        } else {
            return redirect(RouteServiceProvider::USER);
        }
        return view($this->activeTheme.'auth.checkpoint.2fa');
    }

    public function verify2fa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp_code' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back();
        }
        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey(user_auth_info()->google2fa_secret, $request->otp_code);
        if ($valid == false) {
            quick_alert_error(lang('Invalid OTP code', 'auth'));
            return back();
        }
        Session::put('2fa', user_auth_info()->id);
        return redirect(RouteServiceProvider::USER);
    }
}
