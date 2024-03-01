@extends($activeTheme.'layouts.app')
@section('title', lang('Settings', 'account'))
@section('content')
    <div class="d-flex justify-content-between align-items-center pb-30">
        <div class="title-head">
            <h1 class="mb-0">{{ lang('Settings', 'account') }}</h1>
        </div>
    </div>
    <div class="card">
        <ul class="nav nav-tabs " id="settingTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="#edit_profile" class="nav-link active d-flex align-items-center" data-bs-target="#edit_profile" data-bs-toggle="tab">
                    <i class="fas fa-user"></i>
                    <span class="d-none d-md-block ms-2">{{ lang('Edit Profile', 'account') }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#change_password" class="nav-link d-flex align-items-center" data-bs-target="#change_password" data-bs-toggle="tab">
                    <i class="fas fa-key"></i>
                    <span class="d-none d-md-block ms-2">{{ lang('Change Password', 'account') }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#auth" class="nav-link d-flex align-items-center" data-bs-target="#auth" data-bs-toggle="tab">
                    <i class="fas fa-lock"></i>
                    <span class="d-none d-md-block ms-2">{{ lang('2FA Authentication', 'account') }}</span>
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="edit_profile">
                <div>
                    <form id="deatilsForm" action="{{ route('editProfile') }}#edit_profile" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="avatar-upload">
                            <div class="avatar-edit">
                                <input id="change_avatar" type="file" name="avatar"
                                       accept="image/jpg, image/jpeg, image/png" hidden />
                                <label for="change_avatar"></label>
                            </div>
                            <div class="avatar-preview">
                                <div id="imagePreview" style="background-image: url({{ asset('storage/avatars/users/'.user_auth_info()->avatar) }});">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-24">
                            <div class="col">
                                <label for="firstname" class="form-label fw-bold">{{ lang('First Name', 'forms') }} *</label>
                                <input type="text" class="form-control text-field" name="firstname" id="firstname" maxlength="50" placeholder="{{ lang('First Name', 'forms') }}"
                                       value="{{ $user->firstname }}" required>
                            </div>
                            <div class="col">
                                <label for="lastname" class="form-label fw-bold">{{ lang('Last Name', 'forms') }} *</label>
                                <input type="text" name="lastname" class="form-control text-field"
                                       placeholder="{{ lang('Last Name', 'forms') }}" maxlength="50"
                                       value="{{ $user->lastname }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ lang('Username', 'forms') }}</label>
                                    <input class="form-control text-field"
                                           placeholder="{{ lang('Username', 'forms') }}"
                                           value="{{ $user->username }}" readonly disabled>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold">{{ lang('Email address', 'forms') }} *</label>
                                    <input type="email" name="email" class="form-control text-field"
                                           placeholder="{{ lang('Email address', 'forms') }}" value="{{ $user->email }}"
                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ lang('Address', 'forms') }} *</label>
                            <input type="text" name="address" class="form-control text-field"
                                   value="{{ @$user->address->address }}" required>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ lang('City', 'forms') }} *</label>
                                    <input type="text" name="city" class="form-control text-field"
                                           value="{{ @$user->address->city }}" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ lang('State', 'forms') }} *</label>
                                    <input type="text" name="state" class="form-control text-field"
                                           value="{{ @$user->address->state }}" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ lang('Postal Code', 'forms') }} *</label>
                                    <input type="text" name="zip" class="form-control text-field"
                                           value="{{ @$user->address->zip }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ lang('Country', 'forms') }} *</label>
                            <select name="country" class="form-select" required>
                                <option value="" disabled selected>{{ lang('Choose', 'forms') }}</option>
                                @foreach (countries() as $country)
                                    <option value="{{ $country->id }}"
                                        {{ $country->name == @$user->address->country ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="mt-24 button -lg -primary rounded-pill w-100">{{ lang('Save Changes', 'account') }}</button>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="change_password">
                <div class="pt-30">
                    <form id="passwordUpdate" action="{{ route('changePassword') }}#change_password" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ lang('Password', 'forms') }} *</label>
                            <input type="password" class="form-control text-field" name="current-password"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ lang('New Password', 'forms') }} *</label>
                            <input type="password" class="form-control text-field" name="new-password"
                                   required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ lang('Confirm New Password', 'forms') }} *</label>
                            <input type="password" class="form-control text-field"
                                   name="new-password_confirmation" required>
                        </div>
                        <button type="submit" class="mt-24 button -lg -primary rounded-pill w-100">{{ lang('Save Changes', 'account') }}</button>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="auth">
                <div class="pt-30">
                    <div class="settings-box-body">
                        <p class="text-muted">
                            {{ lang('Two-factor authentication significantly decreases the risk of hackers accessing online accounts, blocking 96% of bulk phishing attacks. When digital identity and 2FA are combined, you receive a secure authentication product that is built to scale globally. Authentication factors include one-time password sent to a mobile device.', 'account') }}
                        </p>
                        <p class="text-muted mb-2">
                            {{ lang('Open the authentication app (ex: Authy, Google Authenticator) on your mobile device and scan the following QR Code with your camera.', 'account') }}
                        </p>
                        @if (!$user->google2fa_status)
                            <div class="d-flex flex-column flex-md-row align-items-center">
                                <div class="mb-3 mb-md-0 mr-md-5">
                                    {!! $QR_Image !!}
                                </div>

                                <div>
                                    <label>{{ lang("Can't scan the QR Code?", 'account') }}</label>
                                    <p class="text-muted">{{ lang("Try inserting the following secret code into your app if you can't scan the QR Code.", 'account') }}</p>

                                    <p class="h5">{{ $user->google2fa_secret }}</p>
                                </div>
                            </div>
                            <a href="#" class="mt-24 button -lg -primary rounded-pill w-100" data-bs-toggle="modal"
                               data-bs-target="#towfactorModal">{{ lang('Enable 2FA Authentication', 'account') }}</a>
                        @else
                            <a href="#" class="mt-24 button -lg -danger rounded-pill w-100" data-bs-toggle="modal"
                               data-bs-target="#towfactorDisableModal">{{ lang('Disable 2FA Authentication', 'account') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!$user->google2fa_status)
        <div class="modal fade" id="towfactorModal" tabindex="-1" aria-labelledby="towfactorModalLabel"
             data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('2fa.enable') }}#auth" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ lang('OTP Code', 'forms') }} : <span
                                        class="required">*</span></label>
                                <input type="text" name="otp_code" class="form-control form-control-md input-numeric"
                                       placeholder="••• •••" maxlength="6" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit"
                                        class="btn btn-primary btn-md w-100 me-2">{{ lang('Enable', 'account') }}</button>
                                <button type="button" class="btn btn-light btn-md w-100 ms-2"
                                        data-bs-dismiss="modal">{{ lang('Close') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="modal fade" id="towfactorDisableModal" tabindex="-1" aria-labelledby="towfactorDisableModalLabel"
             data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('2fa.disable') }}#auth" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ lang('OTP Code', 'forms') }} : <span
                                        class="required">*</span></label>
                                <input type="text" name="otp_code" class="form-control form-control-md input-numeric"
                                       placeholder="••• •••" maxlength="6" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit"
                                        class="btn btn-danger btn-md w-100 me-2">{{ lang('Disable', 'account') }}</button>
                                <button type="button" class="btn btn-light btn-md w-100 ms-2"
                                        data-bs-dismiss="modal">{{ lang('Close') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @push('scripts_at_bottom')
        <script>
            var hash = window.location.hash;
            hash && $('#settingTab a[href="' + hash + '"]')[0].click();
            $('#settingTab a').on('click', function (e) {
                window.location.hash = this.hash;
            });
        </script>
    @endpush
@endsection

