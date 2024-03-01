@extends($activeTheme.'layouts.main')
@section('title', lang('Create Post', 'dashboard'))
@section('content')
<div class="container pt-170 pb-100 sm-pb-80">
    <div class="mt-10 text-center">
        <h3 class="mb-1">{{ lang('Add New BioLink', 'dashboard') }}</h3>
        <p>{{ lang('Let’s setup your bio link', 'dashboard') }}</p>
        <div class="row">
            <div class="col-md-6 mx-auto">
                <form id="deatilsForm" action="{{ route('biolinks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <section class="card our-bio-link">
                        <div class="mx-auto sm-mb-32">
                            <div class="bio-link-upload-img">
                                <div class="avatar-upload">
                                    <div class="avatar-edit">
                                        <input id="upload2" type="file" name="logo" onchange="readURL(this,'uploadedAvatar2')"
                                               accept="image/jpg, image/jpeg, image/png" hidden />
                                        <label for="upload2"></label>
                                    </div>
                                    <div class="avatar-preview text-center">
                                        <img src="{{ asset('storage/post/default.png') }}" id="uploadedAvatar2" class="rounded-3"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between flex-column w-100">
                            <input class="form-control text-field mb-16" type="text" name="name" placeholder="{{ lang('Name', 'dashboard') }}" value="{{ old('name') }}">
                            <input class="form-control text-field mb-16" type="text" name="bio" placeholder="{{ lang('Bio', 'dashboard') }}" value="{{ old('bio') }}">
                            <input class="form-control text-field" type="text" name="slug" placeholder="{{ lang('Slug', 'dashboard') }}" value="{{ old('slug') }}">
                            <small class="text-start mt-1">{{ lang('URL slug is just the last part of the URL that serves as an identifier of the page.', 'account') }} {{ lang('Example', 'dashboard') }} : <code>{{ url('/') }}/<strong>{{ lang('Slug', 'dashboard') }}</strong></code></small>
                        </div>

                        <div class="position-relative mt-32">
                            <button class="button -primary w-100 -lg" type="submit">{{ lang('Get Started', 'dashboard') }}</button>
                        </div>
                    </section>
                    <a href="{{ route('dashboard') }}"><i class="fa-regular fa-arrow-left ml-5"></i> {{ lang('Back', 'dashboard') }}</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
