<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Session;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    protected function user()
    {
        return user_auth_info();
    }

    public function index()
    {
        $QR_Image = null;
        if (!$this->user()->google2fa_status) {
            $google2fa = app('pragmarx.google2fa');
            $secretKey = encrypt($google2fa->generateSecretKey());
            User::where('id', $this->user()->id)->update(['google2fa_secret' => $secretKey]);
            $QR_Image = $google2fa->getQRCodeInline(settings('site_title'), $this->user()->email, $this->user()->google2fa_secret);
        }
        return view($this->activeTheme.'.user.settings', ['user' => $this->user(), 'QR_Image' => $QR_Image]);
    }

    public function editProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email,' . $this->user()->id],
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
        $verify = (settings('enable_email_verification') && $this->user()->email != $request->email) ? 1 : 0;
        $country = Country::find($request->country);
        $address = [
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $country->name,
        ];
        if ($request->has('avatar')) {
            if ($this->user()->avatar == 'default.png') {
                $avatar = image_upload($request->file('avatar'), 'storage/avatars/users/', '150x150');
            } else {
                $avatar = image_upload($request->file('avatar'), 'storage/avatars/users/', '150x150', null, $this->user()->avatar);
            }
        } else {
            $avatar = $this->user()->avatar;
        }
        $updateUser = $this->user()->update([
            'name' => $request->firstname . ' ' . $request->lastname,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'address' => $address,
            'avatar' => $avatar,
        ]);
        if ($updateUser) {
            if ($verify) {
                $this->user()->forceFill(['email_verified_at' => null])->save();
                $this->user()->sendEmailVerificationNotification();
            }
            quick_alert_success(lang('Account details has been updated successfully', 'account'));
            return back();
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current-password' => ['required'],
            'new-password' => ['required', 'string', 'min:8', 'confirmed'],
            'new-password_confirmation' => ['required'],
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back();
        }
        if (!(Hash::check($request->get('current-password'), $this->user()->password))) {
            quick_alert_error(lang('Your current password does not matches with the password you provided', 'passwords'));
            return back();
        }
        if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
            quick_alert_error(lang('New Password cannot be same as your current password. Please choose a different password', 'passwords'));
            return back();
        }
        $update = $this->user()->update([
            'password' => bcrypt($request->get('new-password')),
        ]);
        if ($update) {
            quick_alert_success(lang('Account password has been changed successfully', 'account'));
            return back();
        }
    }

    public function towFactorEnable(Request $request)
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
        $valid = $google2fa->verifyKey($this->user()->google2fa_secret, $request->otp_code);
        if ($valid == false) {
            quick_alert_error(lang('Invalid OTP code', 'account'));
            return back();
        }
        $update2FaStatus = User::where('id', $this->user()->id)->update(['google2fa_status' => true]);
        if ($update2FaStatus) {
            Session::put('2fa', $this->user()->id);
            quick_alert_success(lang('2FA Authentication has been enabled successfully', 'account'));
            return back();
        }

    }

    public function towFactorDisable(Request $request)
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
        $valid = $google2fa->verifyKey($this->user()->google2fa_secret, $request->otp_code);
        if ($valid == false) {
            quick_alert_error(lang('Invalid OTP code', 'account'));
            return back();
        }
        $update2FaStatus = User::where('id', $this->user()->id)->update(['google2fa_status' => false]);
        if ($update2FaStatus) {
            if ($request->session()->has('2fa')) {
                Session::forget('2fa');
            }
            quick_alert_success(lang('2FA Authentication has been disabled successfully', 'account'));
            return back();
        }
    }
}
