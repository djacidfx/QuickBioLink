@extends('install.layout')
@section('content')
<div class="quick-card card">
    <div class="card-header">
        <h5 class="text-center">{{ lang('Installation Finish') }}</h5>
    </div>
    <div class="card-body">
        <h5 class="mt-4 text-center">{{ lang('Installed') }}</h5>
        <p class="text-center text-muted mb-3">{!! str_replace('{APP_NAME}','<span class="font-weight-medium">'.env("APP_NAME").'</span>', lang('{APP_NAME} has been installed.')); !!}</p>
        <div class="text-center">
            <a href="{{ route('home') }}" class="btn btn-primary">{{ lang('Go to Home') }}</a>
        </div>
    </div>
</div>

@endsection
