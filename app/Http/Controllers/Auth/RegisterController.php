<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Methods\ReCaptchaValidation;
use App\Models\Country;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::USER;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->activeTheme = active_theme();
    }

    /**
     * Create a new admin notification
     */
    public function createAdminNotify($user)
    {
        $title = $user->name . ' ' . admin_lang('has registered');
        $link = route('admin.users.edit', $user->id);
        admin_notify($title, 'new_user', $link);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        return view($this->activeTheme.'auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'min:2', 'max:50', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['sometimes', 'required'],
        ] + ReCaptchaValidation::validate());
    }

    /**
     * Before register a new user
     *
     * @return //redirect
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $ipInfo = user_ip_info();

        $data = array_merge($request->all(), [
            'country_name' => $ipInfo->location->country,
        ]);
        $user = $this->create($data);
        event(new Registered($user));
        $this->guard()->login($user);
        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['firstname'] . ' ' . $data['lastname'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'username' => $data['username'],
            'email' => $data['email'],
            'address' => ['address' => '', 'city' => '', 'state' => '', 'zip' => '', 'country' => $data['country_name']],
            'avatar' => 'default.png',
            'password' => Hash::make($data['password']),
        ]);
        if ($user) {
            $this->createAdminNotify($user);
            update_user_logs($user);
        }
        return $user;
    }

}
