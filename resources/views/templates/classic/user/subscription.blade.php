@extends($activeTheme.'layouts.app')
@section('title', lang('My Subscription'))
@section('content')
    <div class="d-flex justify-content-between align-items-center pb-30">
        <div class="title-head">
            <h1 class="mb-0">{{ lang('My Subscription') }}</h1>
        </div>
    </div>
    <div class="card">
        <div class="settings-box-body">
            @if ($user->isSubscribed() && !$user->subscription->isCancelled())
                <div class="subscription-box">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-lg">
                            <h4 class="mb-3">{{ $user->subscription->plan->name }}
                                ({{ format_interval($user->subscription->plan->interval) }})</h4>
                            @if (!$user->subscription->isFree())
                                <p class="text-muted mb-0">{{ lang('Next bill date') }} :
                                    <span
                                        class="{{ $user->subscription->isAboutToExpire() ? 'text-warning' : ($user->subscription->isExpired() ? 'text-danger' : 'text') }}">{{ date_formating($user->subscription->expiry_at) }}</span>
                                </p>
                            @else
                                <p class="text-muted mb-0">
                                    {{ str_replace('{period}', $user->subscription->plan->interval ? lang('month') : lang('year'), lang('Your subscription will automatically renew every {period}')) }}
                                </p>
                            @endif
                        </div>
                        <div class="col-12 col-lg-auto">
                            <a href="{{ route('pricing') }}" class="button -primary w-100">
                                <i class="fa-regular fa-circle-up me-1"></i>
                                <span>{{ lang('Change Plan') }}</span>
                            </a>
                        </div>
                        @if (!$user->subscription->isFree() && $user->subscription->isAboutToExpire())
                            <div class="col-12 col-lg-auto">
                                <form
                                    action="{{ route('subscribe', [$user->subscription->plan->id, 'renew']) }}"
                                    method="POST">
                                    @csrf
                                    <button class="button -primary w-100 action-confirm">
                                        <i class="fa-solid fa-rotate me-1"></i>
                                        <span>{{ lang('Renew') }}</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="subscription-box">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-lg">
                            <p class="text-muted mb-0">{{ lang("You are not subscribed to any plan yet.") }}</p>
                        </div>
                        <div class="col-12 col-lg-auto">
                            <a href="{{ route('pricing') }}" class="button -primary w-100">
                                <i class="fa-regular fa-circle-up me-1"></i>
                                <span>{{ lang('Subscribe Now') }}</span>
                            </a>
                        </div>

                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
