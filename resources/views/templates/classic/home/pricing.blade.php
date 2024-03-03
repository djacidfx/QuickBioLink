@extends($activeTheme.'layouts.main')
@section('title', lang('Pricing'))
@section('content')
    <section class="page-banner-area theme-gradient-3 pt-170 @if (ads('home_page_top')) mb-40 @else mb-70 @endif ">
        <div class="container">
            <div class="row wow fadeInUp" data-wow-delay="300ms">
                <div class="col-md-10 col-xl-8 mx-auto">
                    <div class="d-flex flex-column align-items-center">
                        <h2>{{ lang('Pricing') }}</h2>
                        <ol class="breadcrumb text-grey-2">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ lang('Home') }}</a></li>
                            <li class="breadcrumb-item active text-dark-1" aria-current="page">{{ lang('Pricing') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {!! ads_on_home_top() !!}
    <section class="our-pricing pb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto wow fadeInUp" data-wow-delay="300ms">
                    <div class="main-title text-center">
                        <h2 class="title">{{ lang('Membership Plans') }}</h2>
                        <p class="paragraph mt10">{{ lang('Memberships are a great way to build recurring revenue, create engagement, and build deep and meaningful relationships with your fans. Start earning monthly/yearly upfront payments doing what you love!') }}</p>
                    </div>
                </div>
            </div>
            <div class="pricing-table wrapper">
                @if ($yearlyPlans->count() > 0)
                    <div class="d-flex flex-wrap align-items-center justify-content-center">
                        <p class="mb-0 pe-3">{{ lang('Monthly') }}</p>
                        <div class="pricing-switchers">
                            <div class="pricing-switcher pricing-switcher-active"></div>
                            <div class="pricing-switcher"></div>
                            <div class="switcher-button bg-primary"></div>
                        </div>
                        <p class="mb-0 ps-3">{{ lang('Yearly') }}</p>
                    </div>
                @endif

                <div class="plans">
                    <div class="plans-item active">
                        <div class="pricing-table row gx-2 gy-5 mt-2 wow fadeIn">
                            @foreach ($monthlyPlans as $plan)
                                <div class="col-md-4 md-mb-16 {{ $plan->isFeatured() ? 'popular' : '' }}">
                                    <div class="card-inner-wrapper pricing">
                                        <div class="text-center">
                                            <div class="pricing-table-headline mt-16">
                                                <h3 class="title font-18 fw-bold mb-0">
                                                    {{ !empty($plan->translations->{current_language()->code}->name)
                                                        ? $plan->translations->{current_language()->code}->name
                                                        : $plan->name }}
                                                </h3>
                                                <p class="subtitle">
                                                    {{ !empty($plan->translations->{current_language()->code}->short_description)
                                                        ? $plan->translations->{current_language()->code}->short_description
                                                        : $plan->short_description }}
                                                </p>
                                            </div>
                                            <div class="pricing-table-price mt-16 mb-16">
                                                @if ($plan->isFree())
                                                    <span class="price-number">{{ lang('Free') }}</span>
                                                @else
                                                    <span class="price-number">{{ price_symbol_format($plan->price) }}</span>
                                                    <span class="badge">{{ format_interval($plan->interval) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="pricing-table-details mb-16">{{ lang('Get the following deal without any risk and fees.') }}</div>
                                        <div class="pricing-table-features">
                                            <ul class="list-unstyled">
                                                <li class="exist">
                                                    <i class="fa-regular fa-check mr-10 text-success"></i>
                                                    <span>{!! str_replace(
                                                        '{bio_pages_limit}',
                                                        '<strong>' . number_format($plan->settings->biopage_limit) . '</strong>',
                                                        lang('Bio pages limit {bio_pages_limit}'),
                                                    ) !!}</span>
                                                </li>
                                                <li class="exist">
                                                    <i class="fa-regular fa-check mr-10 text-success"></i>
                                                    <span>{!! str_replace(
                                                        '{bio_link_limit}',
                                                        '<strong>' . number_format($plan->settings->biolink_limit) . '</strong>',
                                                        lang('Add link limit {bio_link_limit}'),
                                                    ) !!}</span>
                                                    <i class="fa-regular fa-info-circle" data-bs-toggle="tooltip" title="{{ lang('Per Bio link pages') }}"></i>
                                                </li>
                                                <li class="exist">
                                                    @if ($plan->settings->hide_branding)
                                                        <i class="fa-regular fa-check mr-10 text-success"></i>
                                                    @else
                                                        <i class="fa-regular fa-close mr-10 text-danger"></i>
                                                    @endif
                                                    <span>{{ lang('Hide branding') }}</span>
                                                    <i class="fa-regular fa-info-circle" data-bs-toggle="tooltip" title="{{ lang('Ability to remove the branding from the Bio link pages') }}"></i>
                                                </li>

                                                @if (!$plan->advertisements)
                                                    <li class="exist">
                                                        <i class="fa-regular fa-check mr-10"></i>
                                                        <span>{{ lang('No Advertisements') }}</span>
                                                    </li>
                                                @endif
                                                @if ($plan->custom_features)
                                                    @foreach ($plan->custom_features as $key => $value)
                                                        <li class="exist">
                                                            @foreach (plan_option($key) as $planoption)
                                                                @if ($value == 1)
                                                                    <i class="fa-regular fa-check mr-10 text-success"></i>
                                                                @else
                                                                    <i class="fa-regular fa-close mr-10 text-danger"></i>
                                                                @endif
                                                                <span>
                                                    {{ !empty($planoption->translations->{current_language()->code}->title)
                                                    ? $planoption->translations->{current_language()->code}->title
                                                    : $planoption->title }}
                                                            </span>
                                                            @endforeach
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                        {!! subscribe_button($plan) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if ($yearlyPlans->count() > 0)
                        <div class="plans-item">
                            <div class="row gx-2 gy-5 mt-2">
                                @foreach ($yearlyPlans as $plan)
                                    <div class="col-md-4 md-mb-16 {{ $plan->isFeatured() ? 'popular' : '' }}">
                                        <div class="card-inner-wrapper pricing">
                                            <div class="text-center">
                                                <div class="pricing-table-headline mt-16">
                                                    <h3 class="title font-18 fw-bold mb-0">
                                                        {{ !empty($plan->translations->{current_language()->code}->name)
                                                            ? $plan->translations->{current_language()->code}->name
                                                            : $plan->name }}
                                                    </h3>
                                                    <p class="subtitle">
                                                        {{ !empty($plan->translations->{current_language()->code}->short_description)
                                                            ? $plan->translations->{current_language()->code}->short_description
                                                            : $plan->short_description }}
                                                    </p>
                                                </div>
                                                <div class="pricing-table-price mt-16 mb-16">
                                                    @if ($plan->isFree())
                                                        <span class="price-number">{{ lang('Free') }}</span>
                                                    @else
                                                        <span class="price-number">{{ price_symbol_format($plan->price) }}</span>
                                                        <span class="badge">{{ format_interval($plan->interval) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="pricing-table-details mb-16">{{ lang('Get the following deal without any risk and fees.') }}</div>
                                            <div class="pricing-table-features">
                                                <ul class="list-unstyled">
                                                    <li class="exist">
                                                        <i class="fa-regular fa-check mr-10 text-success"></i>
                                                        <span>{!! str_replace(
                                                        '{bio_pages_limit}',
                                                        '<strong>' . number_format($plan->settings->biopage_limit) . '</strong>',
                                                        lang('Bio pages limit {bio_pages_limit}'),
                                                    ) !!}</span>
                                                    </li>
                                                    <li class="exist">
                                                        <i class="fa-regular fa-check mr-10 text-success"></i>
                                                        <span>{!! str_replace(
                                                        '{bio_link_limit}',
                                                        '<strong>' . number_format($plan->settings->biolink_limit) . '</strong>',
                                                        lang('Add link limit {bio_link_limit}'),
                                                    ) !!}</span>
                                                        <i class="fa-regular fa-info-circle" data-bs-toggle="tooltip" title="{{ lang('Per Bio link pages') }}"></i>
                                                    </li>
                                                    <li class="exist">
                                                        @if ($plan->settings->hide_branding)
                                                            <i class="fa-regular fa-check mr-10 text-success"></i>
                                                        @else
                                                            <i class="fa-regular fa-close mr-10 text-danger"></i>
                                                        @endif
                                                        <span>{{ lang('Hide branding') }}</span>
                                                        <i class="fa-regular fa-info-circle" data-bs-toggle="tooltip" title="{{ lang('Ability to remove the branding from the Bio link pages') }}"></i>
                                                    </li>
                                                    @if (!$plan->advertisements)
                                                        <li class="exist">
                                                            <i class="fa-regular fa-check mr-10 text-success"></i>
                                                            <span>{{ lang('No Advertisements') }}</span>
                                                        </li>
                                                    @endif
                                                    @if ($plan->custom_features)
                                                        @foreach ($plan->custom_features as $key => $value)
                                                            <li class="exist">
                                                                @foreach (plan_option($key) as $planoption)
                                                                    @if ($value == 1)
                                                                        <i class="fa-regular fa-check mr-10 text-success"></i>
                                                                    @else
                                                                        <i class="fa-regular fa-close mr-10 text-danger"></i>
                                                                    @endif
                                                                    <span>
                                                {{ !empty($planoption->translations->{current_language()->code}->title)
                                                ? $planoption->translations->{current_language()->code}->title
                                                : $planoption->title }}
                                                                        </span>
                                                                @endforeach
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </div>
                                            {!! subscribe_button($plan) !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>


    {!! ads_on_home_bottom() !!}
@endsection
