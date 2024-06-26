<div class="tab-pane" id="quick_smtp">
    <form class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" method="POST">
        <div class="card">
            <div class="card-header">
                <h5>{{ lang('SMTP details') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    {{quick_switch(lang('Status'), 'smtp[status]', $settings->smtp->status == '1')}}
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ lang('From Email Address') }}</label>
                            <input type="email" name="smtp[from_email]" class="form-control"
                                   value="{{ demo_mode() ? '' : $settings->smtp->from_email }}"
                                   placeholder="">
                            <small class="form-text">{{lang('This email will be used to send emails.')}}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ lang('From Name') }} </label>
                            <input type="text" name="smtp[from_name]" class="form-control"
                                   value="{{ demo_mode() ? '' : $settings->smtp->from_name }}">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ lang('Mailer') }} </label>
                    <select name="smtp[mailer]" class="form-select">
                        <option value="smtp" @if ($settings->smtp->mailer == 'smtp') selected @endif>
                            {{ lang('SMTP') }}
                        </option>
                        <option value="sendmail" @if ($settings->smtp->mailer == 'sendmail') selected @endif>
                            {{ lang('SENDMAIL') }}
                        </option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ lang('Host') }}</label>
                    <input type="text" name="smtp[host]" class=" form-control"
                           value="{{ demo_mode() ? '' : $settings->smtp->host }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ lang('Port') }}</label>
                    <input type="text" name="smtp[port]" class=" form-control"
                           value="{{ demo_mode() ? '' : $settings->smtp->port }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ lang('Username') }}</label>
                    <input type="text" name="smtp[username]" class="form-control "
                           value="{{ demo_mode() ? '' : $settings->smtp->username }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ lang('Password') }} </label>
                    <input type="password" name="smtp[password]" class="form-control"
                           value="{{ demo_mode() ? '' : $settings->smtp->password }}">
                </div>
                <div>
                    <label class="form-label">{{ lang('Encryption') }} </label>
                    <select name="smtp[encryption]" class="form-select">
                        <option value="tls" @if ($settings->smtp->encryption == 'tls') selected @endif>
                            {{ lang('TLS') }}
                        </option>
                        <option value="ssl" @if ($settings->smtp->encryption == 'ssl') selected @endif>
                            {{ lang('SSL') }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="smtp_settings" value="1">
                <button type="submit" class="btn btn-primary">{{ lang('Save Changes') }}</button>
            </div>
        </div>
    </form>

    <div class="card mt-4">
        <div class="card-header">
            <h5>{{ lang('Test SMTP') }}</h5>
        </div>
        <div class="card-body">
            <form class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" method="POST">
                <div class="mb-3">
                    <label class="form-label">{{ lang('Email Address') }} *</label>
                    <input type="email" name="email" class="form-control" placeholder="john@example.com"
                           value="{{ user_auth_info()->email }}">
                </div>
                <input type="hidden" name="smtp_test" value="1">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i> {{ lang('Send') }}</button>
            </form>
        </div>
    </div>
</div>
