@extends($activeTheme.'layouts.auth')
@section('title', lang('2Fa Verification', 'auth'))
@section('content')
    <div class="container vh-100 py-10">
        <div class="row align-items-center justify-content-center h-100">
            <div class="col-md-5">
                <div class="text-center">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('storage/brand/'.$settings->media->dark_logo) }}"
                             alt="{{ @$settings->site_title }}" class="logo mb-30"/>
                    </a>
                </div>

                <h2 class="text-center font-30 mb-30">{{ lang('2Fa Verification', 'auth') }}</h2>
                <div class="card">
                    <form action="{{ route('2fa.verify') }}" method="POST" class="mt-32">
                        @csrf
                        <div class="form-group mt-16">
                            <input type="text" name="otp_code" class="form-control form-control-md input-numeric" placeholder="••• •••"
                                   maxlength="6" required>
                        </div>
                        <button type="submit" class="button bg-primary text-white mt-32 w-100 rounded-pill -h-48">{{ lang('Continue', 'auth') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
