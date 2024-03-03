<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ lang('Edit FAQ') }}</h2>
            </div>
            <div class="slidePanel-actions">
                <button id="post_sidePanel_data" class="btn btn-icon btn-primary" title="{{ lang('Save') }}">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn btn-default btn-icon slidePanel-close" title="{{ lang('Close') }}">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form action="{{ route('admin.faqs.update', $faq->id) }}" method="post" id="sidePanel_form">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">{{ lang('Language') }} *</label>
                <select name="lang" class="form-select" required>
                    <option value="" selected disabled>{{ lang('Choose') }}</option>
                    @foreach ($adminLanguages as $adminLanguage)
                        <option value="{{ $adminLanguage->code }}" @if ($faq->lang == $adminLanguage->code) selected @endif>
                            {{ $adminLanguage->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Title') }} *</label>
                <input type="text" name="title" class="form-control" value="{{ $faq->title }}" required />
            </div>
            <div class="mb-2">
                <label class="form-label">{{ lang('Content') }} *</label>
                <textarea name="content" class="tiny-editor">{{ $faq->content }}</textarea>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('admin/assets/js/quicklara.js') }}"></script>
