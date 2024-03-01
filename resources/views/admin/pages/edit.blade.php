<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ admin_lang('Edit Page') }}</h2>
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
        <form action="{{ route('admin.pages.update', $page->id) }}" method="post" id="sidePanel_form">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Title') }} *</label>
                <input type="text" name="title" class="form-control" required
                       value="{{ $page->title }}" />
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Slug') }}</label>
                <input type="text" name="slug" class="form-control"
                       value="{{ $page->slug }}" />
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Language') }} *</label>
                <select name="lang" class="form-select" required>
                    <option value="" selected disabled>{{ admin_lang('Choose') }}</option>
                    @foreach ($adminLanguages as $adminLanguage)
                        <option value="{{ $adminLanguage->code }}"
                                @if ($page->lang == $adminLanguage->code) selected @endif>
                            {{ $adminLanguage->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Short description') }} *</label>
                <textarea name="short_description" rows="3" class="form-control" required>{{ $page->short_description }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Content') }} *</label>
                <textarea name="content" rows="10" class="form-control tiny-editor">{{ $page->content }}</textarea>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('admin/assets/js/quicklara.js') }}"></script>
