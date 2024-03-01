<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ admin_lang('Add language') }}</h2>
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
        <form action="{{ route('admin.languages.store') }}" method="post" id="sidePanel_form" enctype="multipart/form-data">
            @csrf
            <div class="d-flex align-items-start justify-content-between gap-4">
                <div>
                    <label for="upload" class="btn btn-primary mb-2" tabindex="0">
                        <i class="fas fa-upload"></i>
                        <span class="d-none d-sm-block ms-2">{{ admin_lang('Upload Flag') }}</span>
                        <input name="flag" type="file" id="upload" hidden
                               onchange="readURL(this,'uploadedFlag')"
                               accept="image/png, image/jpeg">
                    </label>
                    <p class="form-text mb-0">{{ admin_lang('Allowed JPG, JPEG or PNG.') }}</p>
                </div>
                <img src="" alt=""
                     class="d-block rounded" height="50" id="uploadedFlag">
            </div>
            <hr>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Name') }} : <span class="red">*</span></label>
                <input type="text" name="name" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Code') }} : <span class="red">*</span></label>
                <select name="code" class="form-control select2" required>
                    <option></option>
                    @foreach (languages() as $code => $name)
                        <option value="{{ $code }}">{{ $name }} ({{ $code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Direction') }} : <span class="red">*</span></label>
                <select name="direction" class="form-control">
                    <option value="1">{{ admin_lang('LTR') }}</option>
                    <option value="2">{{ admin_lang('RTL') }}</option>
                </select>
            </div>
            <div class="mb-3">
                {{quick_switch(admin_lang('Set Default language'), 'is_default')}}
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('admin/assets/js/quicklara.js') }}"></script>
