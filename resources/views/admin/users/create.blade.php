<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{admin_lang('Add User')}}</h2>
            </div>
            <div class="slidePanel-actions">
                <button id="post_sidePanel_data" class="btn btn-icon btn-primary" title="{{admin_lang('Save')}}">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn btn-icon btn-default slidePanel-close" title="{{admin_lang('Close')}}">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form action="{{ route('admin.users.store') }}" method="post" enctype="multipart/form-data" id="sidePanel_form">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Image') }}</label>
                <div class="d-flex align-items-center gap-4">
                    <img src="{{ asset('storage/avatars/users/default.png') }}" alt=""
                         class="d-block rounded" width="90" id="uploadedImage">
                    <div>
                        <label for="upload" class="btn btn-primary mb-2" tabindex="0">
                            <i class="fas fa-upload"></i>
                            <span class="d-none d-sm-block ms-2">{{ admin_lang('Upload Image') }}</span>
                            <input name="avatar" type="file" id="upload" hidden
                                   onchange="readURL(this,'uploadedImage')"
                                   accept="image/png, image/jpeg" required>
                        </label>
                        <p class="form-text mb-0">{{ admin_lang('Allowed JPG, JPEG or PNG.') }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">{{ admin_lang('First Name') }} : <span class="red">*</span></label>
                        <input type="text" name="firstname" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">{{ admin_lang('Last Name') }} : <span class="red">*</span></label>
                        <input type="text" name="lastname" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Username') }} : <span class="red">*</span></label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('E-mail Address') }} : <span class="red">*</span></label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Country') }} : <span class="red">*</span></label>
                <select name="country" class="form-control" required>
                    <option value="" selected disabled>{{ admin_lang('Choose') }}</option>
                    @foreach (countries() as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">{{ admin_lang('Password') }} : <span class="red">*</span></label>
                <input type="text" name="password" class="form-control" value="{{ $password }}"
                       required>
            </div>
        </form>
    </div>
</div>
