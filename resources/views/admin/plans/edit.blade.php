<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{admin_lang('Edit Plan')}}</h2>
            </div>
            <div class="slidePanel-actions">
                <button id="post_sidePanel_data" class="btn btn-icon btn-primary" title="{{admin_lang('Save')}}">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn btn-icon btn-default slidePanel-close" title="{{admin_lang('Close')}}">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form action="{{ route('admin.plans.update', $plan->id) }}" method="post" enctype="multipart/form-data" id="sidePanel_form">
            @csrf
            @method('PUT')
            <div class="mb-3">
                {{quick_switch(admin_lang('Featured plan'), 'is_featured', $plan->is_featured == '1')}}
            </div>

            <div class="mb-3 form-group">
                <label class="d-flex align-items-end m-b-5" for="name">
                    {{ admin_lang('Plan Name') }} *
                    <div class="d-flex align-items-center translate-picker">
                        <i class="fa fa-language"></i>
                        <select class="custom-select custom-select-sm ml-1">
                            <option value="default">{{ admin_lang('Default') }}</option>
                            @foreach ($adminLanguages as $language)
                                <option value="{{ $language->code }}">{{ $language->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </label>
                <div class="translate-fields translate-fields-default">
                    <input name="name" id="name" type="text" class="form-control" required value="{{ $plan->name }}"
                           placeholder="{{ admin_lang('Enter plan name') }}" autofocus>
                </div>
                @foreach ($adminLanguages as $language)
                    <div class="translate-fields translate-fields-{{ $language->code }}" style="display: none">
                        <input type="text" class="form-control" name="translations[{{ $language->code }}][name]" placeholder="{{ admin_lang('Enter plan name') }}" value="{{ !empty($plan->translations->{$language->code}->name)
                        ? $plan->translations->{$language->code}->name
                        : $plan->name }}">
                    </div>
                @endforeach
            </div>
            <div class="mb-3 form-group">
                <label class="d-flex align-items-end m-b-5" for="name">
                    {{ admin_lang('Short Description') }} *
                    <div class="d-flex align-items-center translate-picker">
                        <i class="fa fa-language"></i>
                        <select class="custom-select custom-select-sm ml-1">
                            <option value="default">{{ admin_lang('Default') }}</option>
                            @foreach ($adminLanguages as $language)
                                <option value="{{ $language->code }}">{{ $language->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </label>
                <div class="translate-fields translate-fields-default">
                    <textarea name="short_description" class="form-control" required>{{ $plan->short_description }}</textarea>
                </div>
                <?php $trans=""; ?>
                @foreach ($adminLanguages as $language)
                    <div class="translate-fields translate-fields-{{ $language->code }}" style="display: none">
                        <textarea name="translations[{{ $language->code }}][short_description]" class="form-control" required>{{ !empty($plan->translations->{$language->code}->short_description)
                        ? $plan->translations->{$language->code}->short_description
                        : $plan->short_description }}</textarea>
                    </div>
                @endforeach
            </div>

            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Plan Interval') }} *</label>
                <select name="interval" class="form-control" disabled required>
                    <option value="1" {{ $plan->interval == 1 ? 'selected' : '' }}>
                        {{ admin_lang('Monthly') }}
                    </option>
                    <option value="2" {{ $plan->interval == 2 ? 'selected' : '' }}>
                        {{ admin_lang('Yearly') }}
                    </option>
                </select>
            </div>
            <div class="mb-3">
                {{quick_switch(admin_lang('Free'), 'is_free', $plan->is_free == '1')}}
            </div>
            <div class="mb-3 plan-price">
                <label class="form-label">{{ admin_lang('Plan Price') }} *</label>
                <div class="custom-input-group input-group">
                    <input type="text" name="price" class="form-control"
                           value="{{ price_format($plan->price) }}" placeholder="0.00" required />
                    <span
                        class="input-group-text"><strong>{{ $settings->currency->code }}</strong></span>
                </div>
            </div>

            <h5 class="m-t-35">{{ admin_lang('Plan Settings') }}</h5>
            <hr>
            <div class="mb-3">
                <label class="form-label" for="biopage_limit">{{ admin_lang('Bio pages limit') }} *</label>
                <input name="biopage_limit" type="number" class="form-control" id="biopage_limit" value="{{ @$plan->settings->biopage_limit }}">
                <span class="form-text text-muted">{{ admin_lang('For unlimited, enter 999') }}</span>
            </div>
            <div class="mb-3">
                <label class="form-label" for="biolink_limit">{{ admin_lang('Add link limit') }} *</label>
                <input name="biolink_limit" type="number" class="form-control" id="biolink_limit" value="{{ @$plan->settings->biolink_limit }}">
                <span class="form-text text-muted">{{ admin_lang('For unlimited, enter 999') }}</span>
            </div>
            <div class="mb-3">
                {{ quick_switch(admin_lang('Hide Branding'), 'hide_branding', @$plan->settings->hide_branding == '1') }}
            </div>
            <div class="mb-3">
                {{quick_switch(admin_lang('Show advertisements'), 'advertisements', $plan->advertisements == '1')}}
            </div>

            <h5 class="m-t-35">{{ admin_lang('Custom Settings') }}</h5>
            <hr>
            @foreach ($PlanOption as $planoption)
                @php
                    $planoption_id = $planoption['id'];
                @endphp
                @if (!empty($planoption['title']) && trim($planoption['title']) != '')
                    {{ quick_switch($planoption['title'], "planoption[".$planoption_id."]", (isset($plan->custom_features->$planoption_id) && $plan->custom_features->$planoption_id == '1')) }}
                @endif
            @endforeach
        </form>
    </div>
</div>
<script>
    // translate picker
    $(document).off('change', ".translate-picker select").on('change', ".translate-picker select", function (e) {
        $('.translate-fields').hide();
        $('.translate-fields-' + $(this).val()).show();
        $('.translate-picker select').val($(this).val());
    });

    $('#is_free').off('change').on('change', function (){
        if($(this).is(':checked'))
            $('.plan-price').slideUp('fast');
        else
            $('.plan-price').slideDown('fast');
    }).trigger('change');
</script>
