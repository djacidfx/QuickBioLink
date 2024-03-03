<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ lang('Edit Category') }}</h2>
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
        <form action="{{ route('admin.categories.update', $category->id) }}" method="post" id="sidePanel_form">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">{{ lang('Language') }} *</label>
                <select name="lang" class="form-select select2" required>
                    <option value="" selected disabled>{{ lang('Choose') }}</option>
                    @foreach ($adminLanguages as $adminLanguage)
                        <option value="{{ $adminLanguage->code }}"
                                @if ($category->lang == $adminLanguage->code) selected @endif>
                            {{ $adminLanguage->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Category name') }} *</label>
                <input type="text" name="name" class="form-control" value="{{ $category->name }}"
                       required />
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Slug') }}</label>
                <input type="text" name="slug" class="form-control" value="{{ $category->slug }}"
                       required />
            </div>
        </form>
    </div>
</div>
