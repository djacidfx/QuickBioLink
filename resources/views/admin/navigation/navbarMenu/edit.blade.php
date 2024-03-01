<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ admin_lang('Edit Link') }}</h2>
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
        <div class="mb-4">
            <form action="{{ route('admin.navbarMenu.update', $navbarMenu->id) }}" method="post" id="sidePanel_form">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">{{ admin_lang('Language') }} *</label>
                    <select name="lang" class="form-select" required>
                        <option value="" selected disabled>{{ admin_lang('Choose') }}</option>
                        @foreach ($adminLanguages as $adminLanguage)
                            <option value="{{ $adminLanguage->code }}" @if ($navbarMenu->lang == $adminLanguage->code) selected @endif>
                                {{ $adminLanguage->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ admin_lang('Name') }} *</label>
                    <input type="text" name="name" class="form-control" value="{{ $navbarMenu->name }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">{{ admin_lang('Link') }} *</label>
                    <input type="link" name="link" class="form-control" placeholder="/"
                           value="{{ $navbarMenu->link }}" required>
                    <small class="form-text">{{ admin_lang('Please enter a full url.') }}</small>
                </div>
            </form>
        </div>
        @include('admin.includes.pre-build-pages')
    </div>
</div>
