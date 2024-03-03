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
    public function index()
    {
        $QR_Image = null;
        if (!user_auth_info()->google2fa_status) {
            $google2fa = app('pragmarx.google2fa');
            $secretKey = encrypt($google2fa->generateSecretKey());

            User::where('id', user_auth_info()->id)->update(['google2fa_secret' => $secretKey]);

            $QR_Image = $google2fa->getQRCodeInline(settings('site_title'), user_auth_info()->email, user_auth_info()->google2fa_secret);
        }
        return view($this->activeTheme.'.user.settings', ['user' => user_auth_info(), 'QR_Image' => $QR_Image]);
    }

    /**
     * Edit user details
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function editProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email,' . user_auth_info()->id],
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

        if ($request->has('avatar')) {
            if (user_auth_info()->avatar == 'default.png') {
                $avatar = image_upload($request->file('avatar'), 'storage/avatars/users/', '150x150');
            } else {
                $avatar = image_upload($request->file('avatar'), 'storage/avatars/users/', '150x150', null, user_auth_info()->avatar);
            }
        } else {
            $avatar = user_auth_info()->avatar;
        }

        $country = Country::find($request->country);

        $updateUser = user_auth_info()->update([
            'name' => $request->firstname . ' ' . $request->lastname,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'avatar' => $avatar,
            'address' => [
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'country' => $country->name,
            ],
        ]);
        if ($updateUser) {
            if (settings('enable_email_verification') && user_auth_info()->email != $request->email) {
                user_auth_info()->forceFill(['email_verified_at' => null])->save();
                user_auth_info()->sendEmailVerificationNotification();
            }
            quick_alert_success(lang('User details updated successfully'));
            return back();
        }
    }

    /**
     * Edit user password
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
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

        if (!(Hash::check($request->get('current-password'), user_auth_info()->password))) {
            quick_alert_error(lang('Current password is incorrect.'));
            return back();
        }
        if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
            quick_alert_error(lang('New password and old password can not be same.'));
            return back();
        }
        $update = user_auth_info()->update([
            'password' => bcrypt($request->get('new-password')),
        ]);
        if ($update) {
            quick_alert_success(lang('Password changed successfully'));
            return back();
        }
    }

    /**
     * Enable 2fa
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function towFAEnable(Request $request)
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
        $valid = $google2fa->verifyGoogle2FA(user_auth_info()->google2fa_secret, $request->otp_code);

        if (!$valid) {
            quick_alert_error(lang('Invalid 2FA OTP Code'));
            return back();
        }

        $update = User::where('id', user_auth_info()->id)->update(['google2fa_status' => true]);
        if ($update) {
            Session::put('2fa', user_auth_info()->id);
            quick_alert_success(lang('2FA Authentication enabled successfully'));
            return back();
        }

    }

    /**
     * Disable 2fa
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function towFADisable(Request $request)
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
        $valid = $google2fa->verifyGoogle2FA(user_auth_info()->google2fa_secret, $request->otp_code);
        if (!$valid) {
            quick_alert_error(lang('Invalid 2FA OTP Code'));
            return back();
        }

        $update = User::where('id', user_auth_info()->id)->update(['google2fa_status' => false]);
        if ($update) {
            if ($request->session()->has('2fa')) {
                Session::forget('2fa');
            }
            quick_alert_success(lang('2FA Authentication disabled successfully'));
            return back();
        }
    }
}
