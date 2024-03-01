@extends('admin.layouts.main')
@section('title', admin_lang('Edit blog'))
@section('content')
    <form action="{{ route('admin.articles.update', $article->id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card p-2 mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ admin_lang('Title') }} *</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ $article->title }}" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ admin_lang('Slug') }}</label>
                            <input type="text" name="slug" class="form-control" value="{{ $article->slug }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ admin_lang('Image') }} *</label>
                            <div class="d-flex align-items-start justify-content-between gap-4">
                                <div>
                                    <label for="upload" class="btn btn-primary mb-2" tabindex="0">
                                        <i class="fas fa-upload"></i>
                                        <span class="d-none d-sm-block ms-2">{{ admin_lang('Upload Image') }}</span>
                                        <input name="image" type="file" id="upload" hidden
                                               onchange="readURL(this,'uploadedImage')"
                                               accept="image/png, image/jpeg">
                                    </label>
                                    <p class="form-text mb-0">{{ admin_lang('Allowed JPG, JPEG or PNG.') }}</p>
                                </div>
                                <img src="{{ asset('storage/blog/articles/'.$article->image) }}" alt=""
                                     class="d-block rounded" width="150" id="uploadedImage">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ admin_lang('Short description') }} *</label>
                            <textarea name="short_description" rows="3" class="form-control"
                                      placeholder="{{ admin_lang('50 to 200 character at most') }}" required>{{ $article->short_description }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">{{ admin_lang('Content') }} : *</label>
                            <textarea name="content" rows="10" class="form-control tiny-editor">{{ $article->content }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card p-2 mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ admin_lang('Language') }} *</label>
                            <select id="articleLang" name="lang" class="form-select" required>
                                <option value="" selected disabled>{{ admin_lang('Choose') }}</option>
                                @foreach ($adminLanguages as $adminLanguage)
                                    <option value="{{ $adminLanguage->code }}"
                                        @if ($article->lang == $adminLanguage->code) selected @endif>
                                        {{ $adminLanguage->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">{{ admin_lang('Category') }} *</label>
                            <select id="articleCategory" name="category" class="form-select" required>
                                <option value="" selected disabled>{{ admin_lang('Choose') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @if ($article->category_id == $category->id) selected @endif>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">{{ admin_lang('Tags') }}</label>
                            <textarea name="tags" rows="2" class="form-control"
                                      placeholder="{{ admin_lang('Enter tags separated by comma') }}" required>{{ $article->tags }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ admin_lang('Submit') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @push('scripts_at_top')
        <script type="text/javascript">
            "use strict";
            var QuickMenu = {"page": "blog", "subpage": "blog-post"};
        </script>
    @endpush
    @push('scripts_vendor')
        <script src="{{ asset('admin/assets/plugins/tinymce/tinymce.min.js') }}"></script>
    @endpush
@endsection
