<div class="tab-pane active" id="quick_settings_general">
    <form method="POST" class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}">
        <div class="quick-card card">
            <div class="card-header">
                <h5>{{ lang('General') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-lg-12">
                        <label class="form-label">{{ lang('Site Title') }} *</label>
                        <input type="text" name="site_title" class="form-control"
                               value="{{ @$settings->site_title }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ lang('Meta Description') }} *</label>
                        <textarea type="text" name="meta_description" class="form-control"
                                   required>{{ @$settings->meta_description }}</textarea>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ lang('Meta Keywords') }} *</label>
                        <textarea type="text" name="meta_keywords" class="form-control"
                                   required>{{ @$settings->meta_keywords }}</textarea>
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(lang('Disable Landing Page'), 'disable_landing_page', @$settings->disable_landing_page == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(lang('New Users Registration'), 'enable_user_registration', @$settings->enable_user_registration == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(lang('Email Verification'), 'enable_email_verification', @$settings->enable_email_verification == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(lang('Force SSL'), 'enable_force_ssl', @$settings->enable_force_ssl == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(lang('Include Language Code in URL'), 'include_language_code', @$settings->include_language_code == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(lang('FAQs'), 'enable_faqs', @$settings->enable_faqs == '1')}}
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ lang('Terms of Service Page Link') }}</label>
                        <input type="url" name="terms_of_service_link" class="form-control"
                               value="{{ @$settings->terms_of_service_link }}">
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(lang('Contact Page'), 'enable_contact_page', @$settings->enable_contact_page == '1')}}
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ lang('Contact email') }} *</label>
                        <input type="email" name="contact_email" class="form-control"
                               value="{{ @$settings->contact_email }}" required>
                        <small class="form-text">{{ lang('This email will be used to send contact emails.') }}</small>
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(lang('Cookie Consent Box'), 'enable_cookie_consent_box', @$settings->enable_cookie_consent_box == '1')}}
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ lang('Cookie Policy Page Link') }}</label>
                        <input type="url" name="cookie_policy_link" class="form-control"
                               value="{{ @$settings->cookie_policy_link }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ lang('Date format') }} *</label>
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
                        <label class="form-label">{{ lang('Timezone') }} *</label>
                        <select name="timezone" class="form-select">
                            @foreach (config('timezones') as $timezoneKey => $timezoneValue)
                                <option value="{{ $timezoneKey }}"
                                    {{ $timezoneKey == @$settings->timezone ? 'selected' : '' }}>
                                    {{ $timezoneValue }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ lang('Facebook Link') }}</label>
                        <input type="url" name="social_links[facebook]" class="form-control"
                               value="{{ @$settings->social_links->facebook }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ lang('Twitter Link') }}</label>
                        <input type="url" name="social_links[twitter]" class="form-control"
                               value="{{ @$settings->social_links->twitter }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ lang('Instagram Link') }}</label>
                        <input type="url" name="social_links[instagram]" class="form-control"
                               value="{{ @$settings->social_links->instagram }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ lang('LinkedIn Link') }}</label>
                        <input type="url" name="social_links[linkedin]" class="form-control"
                               value="{{ @$settings->social_links->linkedin }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ lang('Pinterest Link') }}</label>
                        <input type="url" name="social_links[pinterest]" class="form-control"
                               value="{{ @$settings->social_links->pinterest }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ lang('Youtube Link') }}</label>
                        <input type="url" name="social_links[youtube]" class="form-control"
                               value="{{ @$settings->social_links->youtube }}">
                    </div>
                    <div class="col-lg-6 {{ (env('APP_DEBUG')) ? '' : 'd-none' }}">
                        {{quick_switch(lang('Enable Debug'), 'enable_debug', env('APP_DEBUG'))}}
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="general_setting" value="1">
                <button type="submit" class="btn btn-primary">{{ lang('Save Changes') }}</button>
            </div>
        </div>
    </form>
</div>
