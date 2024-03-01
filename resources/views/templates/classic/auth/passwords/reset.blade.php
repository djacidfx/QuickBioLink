@extends($activeTheme.'layouts.auth')
@section('title', lang('Reset Password', 'auth'))
@section('content')
    <div class="container vh-100 py-10 login-wrapper">
        <div class="row align-items-center justify-content-center h-100">
            <div class="col-md-5">
                <div class="text-center">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('storage/brand/'.$settings->media->dark_logo) }}"
                             alt="{{ @$settings->site_title }}" class="logo mb-30"/>
                    </a>
                </div>
                <div class="card">
                    <h2 class="text-center font-25 mb-30">{{ lang('Reset Password', 'auth') }}</h2>
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group mt-16">
                            <label class="form-label">{{ lang('Email address', 'forms') }} *</label>
                            <input type="email" name="email" class="form-control form-control-md" value="{{ $email }}"
                                   placeholder="{{ lang('Email address', 'forms') }}" readonly />
                        </div>
                        <div class="form-group mt-16">
                            <label class="form-label">{{ lang('Password', 'forms') }} *
                            </label>
                            <input type="password" name="password" class="form-control form-control-md"
                                   placeholder="{{ lang('Password', 'forms') }}" minlength="8" required>
                        </div>
                        <div class="form-group mt-16">
                            <label class="form-label">{{ lang('Confirm password', 'forms') }} *
                            </label>
                            <input type="password" name="password_confirmation" class="form-control form-control-md"
                                   placeholder="{{ lang('Confirm password', 'forms') }}" minlength="8" required>
                        </div>
                        {!! display_captcha() !!}
                        <button type="submit" class="button bg-primary text-white mt-20 w-100 rounded-pill -h-48">{{ lang('Reset', 'auth') }}</button>
                    </form>
                </div>
                <p class="text-center">&copy; <span>{{date("Y")}}</span>
                    {{ @$settings->site_title }} - {{ lang('All rights reserved') }}.</p>
            </div>
        </div>
    </div>
@endsection
