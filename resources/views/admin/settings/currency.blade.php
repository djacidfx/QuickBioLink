<div class="tab-pane" id="quick_currency">
    <form method="post" class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" data-ajax-sidepanel="true">
        <div class="quick-card card">
            <div class="card-header">
                <h5>{{ admin_lang('Currency') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-2">
                    <div class="col-lg-12">
                        <label class="form-label">{{ admin_lang('Currency Code') }} *</label>
                        <input type="text" name="currency[code]" class="form-control"
                               value="{{ $settings->currency->code }}" placeholder="{{ admin_lang('USD') }}"
                               required>
                    </div>
                    <div class="col-lg-12">
                        <label class="form-label">{{ admin_lang('Currency Symbol') }} *</label>
                        <input type="text" name="currency[symbol]" class="form-control"
                               value="{{ $settings->currency->symbol }}" placeholder="{{ admin_lang('$') }}"
                               required>
                    </div>
                    <div class="col-lg-12">
                        <label class="form-label">{{ admin_lang('Currency position') }} *</label>
                        <select name="currency[position]" class="form-select">
                            <option value="1" {{ $settings->currency->position == 1 ? 'selected' : '' }}>
                                {{ admin_lang('Before price') }}</option>
                            <option value="2" {{ $settings->currency->position == 2 ? 'selected' : '' }}>
                                {{ admin_lang('After price') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="currency_setting" value="1">
                <button name="submit" type="submit" class="btn btn-primary">{{ admin_lang('Save Changes') }}</button>
            </div>
        </div>
    </form>
</div>
