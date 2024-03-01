<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ admin_lang('Add tax') }}</h2>
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
        <form action="{{ route('admin.taxes.store') }}" method="post" id="sidePanel_form">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Title') }} *</label>
                <input type="text" name="title" class="form-control" required/>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Description') }} *</label>
                <input type="text" name="description" class="form-control" required/>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Country') }} *</label>
                <select name="country_id" class="form-select select2" required>
                    <option></option>
                    <option value="0">{{ admin_lang('All countries') }}</option>
                    @foreach (countries() as $country)
                        <option value="{{ $country->id }}">
                            {{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Tax percentage') }} *</label>
                <div class="input-group">
                    <input type="number" name="percentage" class="form-control" placeholder="0" required>
                    <span class="input-group-text"><i class="icon-feather-percent"></i></span>
                </div>
            </div>
        </form>
    </div>
</div>
