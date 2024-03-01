<div class="tab-pane" id="quick_testimonial">
    <form class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" method="POST">
        <div class="card">
            <div class="card-header">
                <h5>{{ admin_lang('Testimonials Settings') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    {{quick_switch(admin_lang('Testimonials'), 'testimonials[status]', @$settings->testimonials->status == '1')}}
                </div>
                <div class="mb-3">
                    {{quick_switch(admin_lang('Show On Home Page'), 'testimonials[show_on_home]', @$settings->testimonials->show_on_home == '1')}}
                </div>
                <div class="mb-3" hidden>
                    {{quick_switch(admin_lang('Show On Blog Page'), 'testimonials[show_on_blog]', @$settings->testimonials->show_on_blog == '1')}}
                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="testimonial_settings" value="1">
                <button type="submit" class="btn btn-primary">{{ admin_lang('Save Changes') }}</button>
            </div>
        </div>
    </form>
</div>
