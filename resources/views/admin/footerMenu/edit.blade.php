<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ lang('Edit Link') }}</h2>
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
        <div class="mb-4">
            <form action="{{ route('admin.footerMenu.update', $footerMenu->id) }}" method="post" id="sidePanel_form">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">{{ lang('Language') }} *</label>
                    <select name="lang" class="form-select" required>
                        <option value="" selected disabled>{{ lang('Choose') }}</option>
                        @foreach ($adminLanguages as $adminLanguage)
                            <option value="{{ $adminLanguage->code }}" @if ($footerMenu->lang == $adminLanguage->code) selected @endif>
                                {{ $adminLanguage->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ lang('Name') }} *</label>
                    <input type="text" name="name" class="form-control" value="{{ $footerMenu->name }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">{{ lang('Link') }} *</label>
                    <input type="text" name="link" class="form-control" placeholder="/"
                           value="{{ $footerMenu->link }}" required>
                    <small class="form-text">{{ lang('Please enter a full url.') }}</small>
                </div>

            </form>
        </div>
        @include('admin.includes.pre-build-pages')
    </div>
</div>
