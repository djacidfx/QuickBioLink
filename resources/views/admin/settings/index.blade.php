@extends('admin.layouts.main')
@section('title', lang('Settings'))
@section('content')
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-align-left nav-pills flex-column gap-1" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#quick_settings_general"><i class="fas fa-wrench me-2"></i> {{ lang('General') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#quick_logo_favicon"><i class="fas fa-image me-2"></i> {{ lang('Logo & Favicon') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#quick_colors"><i class="fas fa-paint-brush me-2"></i> {{ lang('Colors') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#quick_currency"><i class="fas fa-coin-front me-2"></i> {{ lang('Currency') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#quick_subscription"><i class="fas fa-user-clock me-2"></i> {{ lang('Subscription') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#quick_smtp"><i class="fas fa-envelope me-2"></i> {{ lang('SMTP Details') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#quick_billing"><i class="fas fa-list-alt me-2"></i> {{ lang('Billing Details') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#quick_social_logins"><i class="fas fa-right-to-bracket me-2"></i> {{ lang('Social Logins') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#quick_addons"><i class="fas fa-puzzle-piece me-2"></i> {{ lang('Add Ons') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#quick_blog"><i class="fas fa-blog me-2"></i> {{ lang('Blog Settings') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#quick_testimonial"><i class="fas fa-star-half-stroke me-2"></i> {{ lang('Testimonial Settings') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#quick_custom_code"><i class="fas fa-code me-2"></i> {{ lang('Custom CSS') }}</button>
                            </li>
                            <li class="nav-item d-none" role="presentation">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#quick_purchase_code"><i class="fas fa-key me-2"></i> {{ lang('Purchase Code') }}</button>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
            <div class="col-md-9">
                <div class="tab-content p-0">
                    @include('admin.settings.general')
                    @include('admin.settings.logo')
                    @include('admin.settings.colors')
                    @include('admin.settings.currency')
                    @include('admin.settings.subscription')
                    @include('admin.settings.smtp')
                    @include('admin.settings.billing-details')
                    @include('admin.settings.social-logins')
                    @include('admin.settings.addons')
                    @include('admin.settings.blog')
                    @include('admin.settings.testimonial')
                    @include('admin.settings.custom-code')
                    @include('admin.settings.purchase-code')
                </div>
            </div>
        </div>

    @push('scripts_at_top')
        <script id="quick-sidebar-menu-js-extra">
            "use strict";
            var QuickMenu = {"page": "settings"};
        </script>
    @endpush
    @push('scripts_at_bottom')
        <script>
            $(function() {
                var hash = window.location.hash;
                hash && $('ul.nav button[data-bs-target="' + hash + '"]').click();
                $('.nav button').on('click', function (e) {
                    var scrollmem = $('body').scrollTop();
                    window.location.hash = $(this).data('bs-target');
                    $('html,body').scrollTop(scrollmem);
                });
            });
        </script>
    @endpush
@endsection
