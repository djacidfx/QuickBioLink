<div class="tab-pane" id="quick_actions">
    <form method="post" class="ajax_submit_form" data-action="{{ route('admin.settings.general.update') }}" data-ajax-sidepanel="true">
        <div class="quick-card card">
            <div class="card-header">
                <h5>{{ lang('Actions') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-2">
                    <div class="col-xl-4">
                        <label class="form-label">{{ lang('Email Verification') }} :</label>
                        <input type="checkbox" name="actions[email_verification_status]" data-toggle="toggle"
                            {{ $settings->actions->email_verification_status ? 'checked' : '' }}>
                    </div>
                    <div class="col-xl-4">
                        <label class="form-label">{{ lang('Website Registration') }} :</label>
                        <input type="checkbox" name="actions[registration_status]" data-toggle="toggle"
                            {{ $settings->actions->registration_status ? 'checked' : '' }}>
                    </div>
                    <div class="col-xl-4">
                        <label class="form-label">{{ lang('Force SSL') }} : </label>
                        <input type="checkbox" name="actions[force_ssl_status]" data-toggle="toggle"
                            {{ $settings->actions->force_ssl_status ? 'checked' : '' }}>
                    </div>
                    <div class="col-xl-4">
                        <label class="form-label">{{ lang('GDPR Cookie') }} : </label>
                        <input type="checkbox" name="actions[gdpr_cookie_status]" data-toggle="toggle"
                            {{ $settings->actions->gdpr_cookie_status ? 'checked' : '' }}>
                    </div>
                    <div class="col-xl-4">
                        <label class="form-label">{{ lang('Website blog') }} :</label>
                        <input type="checkbox" name="actions[blog_status]" data-toggle="toggle"
                            {{ $settings->actions->blog_status ? 'checked' : '' }}>
                    </div>
                    <div class="col-xl-4">
                        <label class="form-label">{{ lang('Contact Page') }} : </label>
                        <input type="checkbox" name="actions[contact_page]" data-toggle="toggle"
                               data-on="{{ lang('Enable') }}" data-off="{{ lang('Disable') }}"
                            {{ $settings->actions->contact_page ? 'checked' : '' }}>
                    </div>
                    <div class="col-xl-4">
                        <label class="form-label">{{ lang('Features Page') }} : </label>
                        <input type="checkbox" name="actions[features_page]" data-toggle="toggle"
                               data-on="{{ lang('Enable') }}" data-off="{{ lang('Disable') }}"
                            {{ $settings->actions->features_page ? 'checked' : '' }}>
                    </div>
                    <div class="col-xl-4">
                        <label class="form-label">{{ lang('FAQs Status') }} : </label>
                        <input type="checkbox" name="actions[faqs_status]" data-toggle="toggle"
                            {{ $settings->actions->faqs_status ? 'checked' : '' }}>
                    </div>
                    <div class="col-xl-4">
                        <label class="form-label">{{ lang('Include language code in URL') }} : </label>
                        <input type="checkbox" name="actions[language_type]" data-toggle="toggle"
                               data-on="{{ lang('Yes') }}" data-off="{{ lang('No') }}"
                            {{ $settings->actions->language_type ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="actions_setting" value="1">
                <button name="submit" type="submit" class="btn btn-primary">{{ lang('Save') }}</button>
            </div>
        </div>
    </form>
</div>
