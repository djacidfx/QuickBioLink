<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{admin_lang('Edit Plan Option')}}</h2>
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
        <form action="{{ route('admin.planoption.update', $planoption->id) }}" method="post" enctype="multipart/form-data" id="sidePanel_form">
            @csrf
            @method('PUT')

            <div class="mb-3 form-group">
                <label class="d-flex align-items-end m-b-5" for="name">
                    {{ admin_lang('Title') }} *
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
                    <input name="title" id="title" type="text" class="form-control" required value="{{ $planoption->title }}"
                           placeholder="{{ admin_lang('Enter plan option title') }}" autofocus>
                </div>
                @foreach ($adminLanguages as $language)
                    <div class="translate-fields translate-fields-{{ $language->code }}" style="display: none">
                        <input type="text" class="form-control" name="translations[{{ $language->code }}][title]" placeholder="{{ admin_lang('Enter plan option title') }}" value="{{ !empty($planoption->translations->{$language->code}->title)
                        ? $planoption->translations->{$language->code}->title
                        : $planoption->title }}">
                    </div>
                @endforeach
            </div>

            <div class="mb-3">
                {{ quick_switch(admin_lang('Enable/Disable'), 'active', $planoption->active == 1) }}
            </div>

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
</script>