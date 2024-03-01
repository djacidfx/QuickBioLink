@extends($activeTheme.'layouts.auth')
@section('title', lang('Verify Your Email Address', 'auth'))
@section('content')
    <div class="container vh-100 py-10 login-wrapper">
        <div class="row align-items-center justify-content-center h-100">
            <div class="col-md-5 text-center">
                <div class="mb-30">
                    <i class="text-primary font-80 far fa-envelope-circle-check"></i>
                </div>
                <div>
                    <h2 class="mb-30">{{ lang('Verify Your Email Address', 'auth') }}</h2>
                    <p class="mb-30">
                        {{ lang('Your email address is not verified, please verify your email. If you haven\'t received the verification email, please check your spam or junk folder, or request a new verification email to be sent.', 'auth') }}
                    </p>
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="button -primary -lg me-2">{{ lang('Resend', 'auth') }}</button>
                    </form>
                    <button href="#" class="button -secondary -lg" data-bs-toggle="modal"
                            data-bs-target="#emailModal">{{ lang('Change Email', 'auth') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="emailModal" tabindex="-1"
        aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ lang('Change Email', 'auth') }}</h5>
                    <button type="button" class="icon-group -close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-regular fa-xmark"></i>
                    </button>
                </div>
                <form action="{{ route('change.email') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ lang('Email address', 'forms') }} *</label>
                            <input type="email" name="email" id="email" class="form-control form-control-md"
                                placeholder="{{ lang('Email address', 'forms') }}" value="{{ user_auth_info()->email }}"
                                required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit"
                                class="button -primary w-100">{{ lang('Save', 'auth') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
