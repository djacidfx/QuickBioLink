<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ admin_lang('Edit SEO Configuration') }}</h2>
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
        <form action="{{ route('admin.seo.update', $configuration->id) }}" method="post" id="sidePanel_form">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Language') }} *</label>
                <select name="lang" class="form-select" required>
                    <option></option>
                    @foreach ($languages as $language)
                        <option value="{{ $language->code }}" @if ($configuration->lang == $language->code) selected @endif>
                            {{ $language->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Site Title') }} *</label>
                <input type="text" name="title" class="form-control"
                       placeholder="Title must be within 70 Characters" value="{{ $configuration->title }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Site Description') }} *</label>
                <textarea name="description" class="form-control" rows="3" placeholder="150 Characters Max"
                          required>{{ $configuration->description }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Site Keywords') }} *</label>
                <textarea id="keywords" name="keywords" class="form-control" rows="3" placeholder="keyword1, keyword2, keyword3"
                          required>{{ $configuration->keywords }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Allow robots to index your website') }} *</label>
                <select name="robots_index" class="form-select" required>
                    <option value="index" @if ($configuration->robots_index == 'index') selected @endif>
                        {{ admin_lang('Yes') }}</option>
                    <option value="noindex" @if ($configuration->robots_index == 'noindex') selected @endif>
                        {{ admin_lang('No') }}</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Allow robots to follow all links') }} *</label>
                <select name="robots_follow_links" class="form-select" required>
                    <option value="follow" @if ($configuration->robots_follow_links == 'follow') selected @endif>
                        {{ admin_lang('Yes') }}</option>
                    <option value="nofollow" @if ($configuration->robots_follow_links == 'nofollow') selected @endif>
                        {{ admin_lang('No') }}</option>
                </select>
            </div>
        </form>
    </div>
</div>
