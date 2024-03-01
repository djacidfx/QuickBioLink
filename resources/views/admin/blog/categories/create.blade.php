<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ admin_lang('Create New Category') }}</h2>
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
        <form action="{{ route('admin.categories.store') }}" method="post" id="sidePanel_form">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Language') }} *</label>
                <select name="lang" class="form-select select2" required>
                    <option value="" selected disabled>{{ admin_lang('Choose') }}</option>
                    @foreach ($adminLanguages as $adminLanguage)
                        <option value="{{ $adminLanguage->code }}">
                            {{ $adminLanguage->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Category name') }} *</label>
                <input type="text" name="name" class="form-control"
                       value="" required autofocus />
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Slug') }}</label>
                <input type="text" name="slug" class="form-control" value=""
                       required />
            </div>
        </form>
    </div>
</div>
