<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ lang('New SEO Configuration') }}</h2>
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
        <form action="{{ route('admin.seo.store') }}" method="post" id="sidePanel_form">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ lang('Language') }} *</label>
                <select name="lang" class="form-control" required>
                    <option></option>
                    @foreach ($languages as $language)
                        <option value="{{ $language->code }}">
                            {{ $language->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Site Title') }} *</label>
                <input type="text" name="title" class="form-control" value="" required>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Site Description') }} *</label>
                <textarea name="description" class="form-control" rows="3" placeholder="150 Characters Max"
                          required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Site Keywords') }} *</label>
                <textarea id="keywords" name="keywords" class="form-control" rows="3" placeholder="keyword1, keyword2, keyword3"
                          required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Allow robots to index your website') }} *</label>
                <select name="robots_index" class="form-select" required>
                    <option value="index" @if (old('robots_index') == 'index') selected @endif>
                        {{ lang('Yes') }}</option>
                    <option value="noindex" @if (old('robots_index') == 'noindex') selected @endif>
                        {{ lang('No') }}</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Allow robots to follow all links') }} *</label>
                <select name="robots_follow_links" class="form-select" required>
                    <option value="follow" @if (old('robots_follow_links') == 'follow') selected @endif>
                        {{ lang('Yes') }}</option>
                    <option value="nofollow" @if (old('robots_follow_links') == 'nofollow') selected @endif>
                        {{ lang('No') }}</option>
                </select>
            </div>
        </form>
    </div>
</div>
