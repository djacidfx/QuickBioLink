<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ lang('Create Page') }}</h2>
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
        <form action="{{ route('admin.pages.store') }}" method="post" id="sidePanel_form">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ lang('Title') }} *</label>
                <input type="text" name="title" class="form-control"
                       value="" required />
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Slug') }}</label>
                <input type="text" name="slug" class="form-control"
                       value="" />
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Language') }} *</label>
                <select name="lang" class="form-select" required>
                    <option value="" selected disabled>{{ lang('Choose') }}</option>
                    @foreach ($adminLanguages as $adminLanguage)
                        <option value="{{ $adminLanguage->code }}">
                            {{ $adminLanguage->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Short Description') }} *</label>
                <textarea name="short_description" rows="3" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Content') }} *</label>
                <textarea name="content" rows="10" class="form-control tiny-editor"></textarea>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('admin/assets/js/quicklara.js') }}"></script>
