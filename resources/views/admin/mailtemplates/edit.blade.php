<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ admin_lang('Edit Mail Template') }}</h2>
            </div>
            <div class="slidePanel-actions">
                <button id="post_sidePanel_data" class="btn btn-icon btn-primary" title="{{ admin_lang('Save') }}">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn btn-default btn-icon slidePanel-close" title="{{ admin_lang('Close') }}">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form action="{{ route('admin.mailtemplates.update', $mailTemplate->id) }}" method="post" id="sidePanel_form">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Subject') }} *</label>
                <input type="text" name="subject" class="form-control" value="{{ $mailTemplate->subject }}"
                       required>
            </div>
            @if (!$mailTemplate->undisable())
            <div class="mb-3">
                {{quick_switch(admin_lang('Status'), 'status', $mailTemplate->status == '1')}}
            </div>
            @endif
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Body') }} *</label>
                <textarea name="body" class="tiny-editor">{{ $mailTemplate->body }}</textarea>
            </div>
            <label class="form-label">{{ admin_lang('Shortcodes') }}</label>
            <div class="quick-shortcode-wrapper">
                @foreach ($mailTemplate->shortcodes as $key => $value)
                    <div class="quick-shortcode-box">
                        <div class="bg-light" data-tippy-placement="top" title="{{ $value }}">@php echo "{{". $key ."}}"  @endphp</div>
                        <button class="btn btn-default btn-icon" data-tippy-placement="top" type="button" data-code="@php echo "{{". $key ."}}"  @endphp" title="{{ admin_lang('Copy') }}"><i class="icon-feather-copy"></i></button>
                    </div>
                @endforeach
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('admin/assets/js/quicklara.js') }}"></script>
