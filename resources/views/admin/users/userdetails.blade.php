<div class="quick-card card">
    <div class="card-body">
        <div class="user-avatar-section mb-2">
            <div class="d-flex align-items-center flex-column">
                <img id="filePreview" class="img-fluid rounded my-3"
                     src="{{ asset('storage/avatars/users/'.$user->avatar) }}" height="110" width="110"
                     alt="User avatar">
                <div class="user-info text-center mt-2">
                    <h4 class="mb-2">{{ $user->name }}</h4>
                </div>
            </div>
        </div>
    </div>
    <ul class="custom-list-group list-group list-group-flush border-top">
        <li class="list-group-item d-flex justify-content-between"><span>{{ lang('Username') }} :</span>
            <strong>{{ $user->username }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between"><span>{{ lang('Email') }} :</span>
            <strong>{{ $user->email }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between"><span>{{ lang('Status') }} :</span>
            @if ($user->status)
                <span class="badge bg-success">{{lang('Active')}}</span>
            @else
                <span class="badge bg-danger">{{lang('Banned')}}</span>
            @endif
        </li>
        <li class="list-group-item d-flex justify-content-between"><span>{{ lang('Email Verify') }} :</span>
            @if ($user->email_verified_at)
                <span class="badge bg-success">{{lang('Verified')}}</span>
            @else
                <span class="badge bg-warning">{{lang('Unverified')}}</span>
            @endif
        </li>
        <li class="list-group-item d-flex justify-content-between"><span>{{ lang('Joined at') }} :</span>
            <strong>{{ date_formating($user->created_at) }}</strong>
        </li>
    </ul>
</div>
@if ($user->isSubscribed())
    <div class="quick-card card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">{{$user->subscription->plan->name}}</h4>
                <div class="d-flex justify-content-center">
                    <h1 class="display-5 mb-0 text-primary">{{price_symbol_format($user->subscription->plan->price)}}</h1>
                    <sub
                        class="fs-6 pricing-duration mt-auto mb-3">/{{$user->subscription->plan->interval == 1 ? 'month' : 'year'}}</sub>
                </div>
            </div>
            @if ($user->subscription->isCancelled())
                <span class="badge bg-label-warning">
                    {{lang('Canceled')}}
                </span>
            @elseif ($user->subscription->isExpired())
                <span class="badge bg-label-danger">
                    {{lang('Expired')}}
                </span>
            @endif
            <ul class="ps-3 g-2 my-4 list-unstyled">
                <li class="mb-2">
                    <i class="fa-regular fa-check mr-10 text-success"></i>
                    <span>{!! str_replace(
                        '{bio_pages_limit}',
                        '<strong>' . number_format($user->subscription->plan_settings->biopage_limit) . '</strong>',
                        lang('Bio pages limit {bio_pages_limit}'),
                    ) !!}</span>
                </li>
                <li class="mb-2">
                    <i class="fa-regular fa-check mr-10 text-success"></i>
                    <span>{!! str_replace(
                        '{bio_link_limit}',
                        '<strong>' . number_format($user->subscription->plan_settings->biolink_limit) . '</strong>',
                        lang('Add link limit {bio_link_limit}'),
                    ) !!}</span>
                    <i class="fa-regular fa-info-circle" data-bs-toggle="tooltip" title="{{ lang('Per Bio link pages') }}"></i>
                </li>
                <li class="mb-2">
                    @if ($user->subscription->plan_settings->hide_branding)
                        <i class="fa-regular fa-check mr-10 text-success"></i>
                    @else
                        <i class="fa-regular fa-close mr-10 text-danger"></i>
                    @endif
                    <span>{{ lang('Hide branding') }}</span>
                    <i class="fa-regular fa-info-circle" data-bs-toggle="tooltip" title="{{ lang('Ability to remove the branding from the Bio link pages') }}"></i>
                </li>
                @if (!$user->subscription->plan->advertisements)
                    <li class="mb-2">
                        <i class="fa-regular fa-check mr-10"></i>
                        <span>{{ lang('No Advertisements') }}</span>
                    </li>
                @endif
                @if ($user->subscription->plan->custom_features)
                    @foreach ($user->subscription->plan->custom_features as $key => $value)
                        <li class="mb-2">
                            @foreach (plan_option($key) as $planoption)
                                @if ($value == 1)
                                    <i class="fa-regular fa-check mr-10 text-success"></i>
                                @else
                                    <i class="fa-regular fa-close mr-10 text-danger"></i>
                                @endif
                                <span>{{ !empty($planoption->translations->{current_language()->code}->title)
                                    ? $planoption->translations->{current_language()->code}->title
                                    : $planoption->title }}</span>
                            @endforeach
                        </li>
                    @endforeach
                @endif
            </ul>
            <div class="d-grid w-100 mt-4 pt-2">
                <button class="btn btn-primary"
                        data-url="{{ route('admin.subscriptions.edit', $user->subscription->id) }}"
                        data-toggle="slidePanel"><i class="icon-feather-gift me-2"></i>{{ lang('Update Plan') }}
                </button>
            </div>
        </div>
    </div>
@endif
