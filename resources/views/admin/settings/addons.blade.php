<div class="tab-pane" id="quick_addons">
    <form class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" method="POST">
        <div class="card">
            <div class="card-header">
                <h5>{{ lang('Add Ons') }}</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="accordions">

                    <div class="card accordion-item mb-3">
                        <h2 class="accordion-header" id="heading_recaptcha">
                            <button type="button" class="accordion-button fw-semibold collapsed" data-bs-toggle="collapse" data-bs-target="#recaptcha" aria-expanded="false" aria-controls="recaptcha">
                                {{ lang('Google reCAPTCHA') }}
                            </button>
                        </h2>

                        <div id="recaptcha" class="accordion-collapse collapse" data-bs-parent="#accordions" style="">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    <h5 class="mb-2">{{lang('Instructions')}}</h5>
                                    <p class="mb-2">{{lang('To find your Site Key and Secret Key, follow the below steps:')}}</p>
                                    <ol>
                                        <li><a href="https://www.google.com/recaptcha/admin/create" target="_blank">{{lang('Go to the Google reCAPTCHA and register a new site.')}}</a></li>
                                        <li>{{lang('Enter label and select "reCAPTCHA v2" -> "I\'m not a robot" Checkbox in reCAPTCHA type field.')}}</li>
                                        <li>{{lang('Enter your domain url.')}}</li>
                                        <li>{{lang('Accept Terms of Service and click on the Submit button.')}}</li>
                                        <li>{{lang('Look for the Site Key and Secret Key. Use them in the form below on this page.')}}</li>
                                    </ol>
                                </div>
                                <div class="mb-3">
                                    {{quick_switch(lang('Status'), 'google_recaptcha[status]', @$settings->google_recaptcha->status == '1')}}
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ lang('ReCAPTCHA Site Key') }} </label>
                                            <input type="text" name="google_recaptcha[site_key]" class="form-control"
                                                   value="{{ demo_mode() ? '' : @$settings->google_recaptcha->site_key }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ lang('ReCAPTCHA Secret Key') }} </label>
                                            <input type="text" name="google_recaptcha[secret_key]" class="form-control"
                                                   value="{{ demo_mode() ? '' : @$settings->google_recaptcha->secret_key }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card accordion-item mb-3">
                        <h2 class="accordion-header" id="heading_analytics">
                            <button type="button" class="accordion-button fw-semibold collapsed" data-bs-toggle="collapse" data-bs-target="#analytics" aria-expanded="false" aria-controls="analytics">
                                {{ lang('Google Analytics 4') }}
                            </button>
                        </h2>
                        <div id="analytics" class="accordion-collapse collapse" data-bs-parent="#accordions" style="">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    {{quick_switch(lang('Status'), 'google_analytics[status]', @$settings->google_analytics->status == '1')}}
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ lang('Measurement Id') }} </label>
                                    <input type="text" name="google_analytics[measurement_id]" class="form-control"
                                           value="{{ demo_mode() ? '' : @$settings->google_analytics->measurement_id }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card accordion-item mb-3">
                        <h2 class="accordion-header" id="heading_tawkto">
                            <button type="button" class="accordion-button fw-semibold collapsed" data-bs-toggle="collapse" data-bs-target="#tawkto" aria-expanded="false" aria-controls="tawkto">
                                {{ lang('Tawk.to (Live Chat)') }}
                            </button>
                        </h2>
                        <div id="tawkto" class="accordion-collapse collapse" data-bs-parent="#accordions" style="">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    {{quick_switch(lang('Status'), 'tawk_to[status]', @$settings->tawk_to->status == '1')}}
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ lang('Direct Chat Link') }} </label>
                                    <input type="text" name="tawk_to[chat_link]" class="form-control"
                                           value="{{ demo_mode() ? '' : @$settings->tawk_to->chat_link }}">
                                    <small class="form-text"><a href="https://help.tawk.to/article/direct-chat-link" target="_blank">{{lang('You can find here.')}}</a></small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="addons_settings" value="1">
                <button type="submit" class="btn btn-primary">{{ lang('Save Changes') }}</button>
            </div>
        </div>
    </form>
</div>
