<div class="tab-pane active" id="quick_settings_general">
    <form method="POST" class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}">
        <div class="quick-card card">
            <div class="card-header">
                <h5>{{ admin_lang('General') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Site Title') }} *</label>
                        <input type="text" name="site_title" class="form-control"
                               value="{{ @$settings->site_title }}" required>
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(admin_lang('Disable Landing Page'), 'disable_landing_page', @$settings->disable_landing_page == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(admin_lang('New Users Registration'), 'enable_user_registration', @$settings->enable_user_registration == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(admin_lang('Email Verification'), 'enable_email_verification', @$settings->enable_email_verification == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(admin_lang('Force SSL'), 'enable_force_ssl', @$settings->enable_force_ssl == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(admin_lang('Include Language Code in URL'), 'include_language_code', @$settings->include_language_code == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(admin_lang('FAQs'), 'enable_faqs', @$settings->enable_faqs == '1')}}
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Terms of Service Page Link') }}</label>
                        <input type="url" name="terms_of_service_link" class="form-control"
                               value="{{ @$settings->terms_of_service_link }}">
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(admin_lang('Contact Page'), 'enable_contact_page', @$settings->enable_contact_page == '1')}}
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Contact email') }} *</label>
                        <input type="email" name="contact_email" class="form-control"
                               value="{{ @$settings->contact_email }}" required>
                        <small class="form-text">{{ admin_lang('This email will be used to send contact emails.') }}</small>
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(admin_lang('Cookie Consent Box'), 'enable_cookie_consent_box', @$settings->enable_cookie_consent_box == '1')}}
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Cookie Policy Page Link') }}</label>
                        <input type="url" name="cookie_policy_link" class="form-control"
                               value="{{ @$settings->cookie_policy_link }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Date format') }} *</label>
                        <select name="date_format" class="form-select">
                            @foreach (date_formats_array() as $formatKey => $formatValue)
                                <option value="{{ $formatKey }}"
                                    {{ $formatKey == @$settings->date_format ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::now()->format($formatValue) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Timezone') }} *</label>
                        <select name="timezone" class="form-select">
                            @foreach (timezones_array() as $timezoneKey => $timezoneValue)
                                <option value="{{ $timezoneKey }}"
                                    {{ $timezoneKey == @$settings->timezone ? 'selected' : '' }}>
                                    {{ $timezoneValue }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Facebook Link') }}</label>
                        <input type="url" name="social_links[facebook]" class="form-control"
                               value="{{ @$settings->social_links->facebook }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Twitter Link') }}</label>
                        <input type="url" name="social_links[twitter]" class="form-control"
                               value="{{ @$settings->social_links->twitter }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Instagram Link') }}</label>
                        <input type="url" name="social_links[instagram]" class="form-control"
                               value="{{ @$settings->social_links->instagram }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('LinkedIn Link') }}</label>
                        <input type="url" name="social_links[linkedin]" class="form-control"
                               value="{{ @$settings->social_links->linkedin }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Pinterest Link') }}</label>
                        <input type="url" name="social_links[pinterest]" class="form-control"
                               value="{{ @$settings->social_links->pinterest }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Youtube Link') }}</label>
                        <input type="url" name="social_links[youtube]" class="form-control"
                               value="{{ @$settings->social_links->youtube }}">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="general_setting" value="1">
                <button type="submit" class="btn btn-primary">{{ admin_lang('Save Changes') }}</button>
            </div>
        </div>
    </form>
</div>
