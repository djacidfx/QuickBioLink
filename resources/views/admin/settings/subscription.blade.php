<div class="tab-pane" id="quick_subscription">
    <form method="post" class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" data-ajax-sidepanel="true">
        <div class="quick-card card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>{{ lang('Subscription') }}</h5>
                <a href="{{ route('admin.mailtemplates.index') }}"
                   class="btn btn-secondary btn-sm">{{ lang('Mail Templates') }}</a>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-2">
                    <div class="col-lg-12">
                        <label class="form-label">{{ lang('Subscription About to expire reminder') }}
                            *</label>
                        <select name="subscription[about_to_expire_reminder]" class="form-select">
                            <option value="1"
                                {{ $settings->subscription->about_to_expire_reminder == 1 ? 'selected' : '' }}>
                                {{ lang('Before One day') }}</option>
                            <option value="2"
                                {{ $settings->subscription->about_to_expire_reminder == 2 ? 'selected' : '' }}>
                                {{ lang('Before 2 days') }}</option>
                            <option value="3"
                                {{ $settings->subscription->about_to_expire_reminder == 3 ? 'selected' : '' }}>
                                {{ lang('Before 3 days') }}</option>
                            <option value="7"
                                {{ $settings->subscription->about_to_expire_reminder == 7 ? 'selected' : '' }}>
                                {{ lang('Before 7 days') }}</option>
                            <option value="14"
                                {{ $settings->subscription->about_to_expire_reminder == 14 ? 'selected' : '' }}>
                                {{ lang('Before 14 days') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <label class="form-label">{{ lang('Subscription Expired Reminder') }} *</label>
                        <select name="subscription[expired_reminder]" class="form-select">
                            <option value="1"
                                {{ $settings->subscription->expired_reminder == 1 ? 'selected' : '' }}>
                                {{ lang('After One day') }}</option>
                            <option value="2"
                                {{ $settings->subscription->expired_reminder == 2 ? 'selected' : '' }}>
                                {{ lang('After 2 days') }}</option>
                            <option value="3"
                                {{ $settings->subscription->expired_reminder == 3 ? 'selected' : '' }}>
                                {{ lang('After 3 days') }}</option>
                            <option value="7"
                                {{ $settings->subscription->expired_reminder == 7 ? 'selected' : '' }}>
                                {{ lang('After 7 days') }}</option>
                            <option value="14"
                                {{ $settings->subscription->expired_reminder == 14 ? 'selected' : '' }}>
                                {{ lang('After 14 days') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <label class="form-label">{{ lang('Delete Expired Subscriptions') }} *</label>
                        <select name="subscription[delete_expired]" class="form-select">
                            <option value="3"
                                {{ $settings->subscription->delete_expired == 3 ? 'selected' : '' }}>
                                {{ lang('After 3 days') }}</option>
                            <option value="7"
                                {{ $settings->subscription->delete_expired == 7 ? 'selected' : '' }}>
                                {{ lang('After 7 days') }}</option>
                            <option value="14"
                                {{ $settings->subscription->delete_expired == 14 ? 'selected' : '' }}>
                                {{ lang('After 14 days') }}</option>
                            <option value="30"
                                {{ $settings->subscription->delete_expired == 30 ? 'selected' : '' }}>
                                {{ lang('After 1 Month') }}</option>
                            <option value="60"
                                {{ $settings->subscription->delete_expired == 60 ? 'selected' : '' }}>
                                {{ lang('After 3 Months') }}</option>
                            <option value="120"
                                {{ $settings->subscription->delete_expired == 120 ? 'selected' : '' }}>
                                {{ lang('After 6 Months') }}</option>
                            <option value="365"
                                {{ $settings->subscription->delete_expired == 365 ? 'selected' : '' }}>
                                {{ lang('After 1 Year') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="subscription_setting" value="1">
                <button name="submit" type="submit" class="btn btn-primary">{{ lang('Save') }}</button>
            </div>
        </div>
    </form>
</div>
