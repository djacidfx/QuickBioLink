@extends('admin.layouts.main')
@section('title', admin_lang('System Information'))
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5>{{ admin_lang('Application') }}</h5>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                <strong class="text-capitalize">{{ str_replace('_', ' ', $system->application->name) }} {{ admin_lang('Version') }}</strong>
                <span>v{{ $system->application->version }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center ">
                <strong>{{ admin_lang('Laravel Version') }}</strong>
                <span>v{{ $system->application->laravel }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                <strong>{{ admin_lang('Timezone') }}</strong>
                <span>{{ $system->application->timezone }}</span>
            </li>
        </ul>
    </div>
    <div class="card mb-4">
        <div class="card-header">
            <h5>{{ admin_lang('Server Details') }}</h5>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                <strong>{{ admin_lang('PHP Version') }}</strong>
                <span>v{{ $system->server->php }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>{{ admin_lang('Server Software') }}</strong>
                <span>{{ $system->server->SERVER_SOFTWARE }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>{{ admin_lang('Server IP Address') }}</strong>
                <span>{{ $system->server->SERVER_ADDR }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                <strong>{{ admin_lang('Server Protocol') }}</strong>
                <span>{{ $system->server->SERVER_PROTOCOL }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>{{ admin_lang('HTTP Host') }}</strong>
                <span>{{ $system->server->HTTP_HOST }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                <strong>{{ admin_lang('Server Port') }}</strong>
                <span>{{ $system->server->SERVER_PORT }}</span>
            </li>
        </ul>
    </div>
    <div class="card">
        <div class="card-header">
            <h5>{{ admin_lang('Clear System Cache') }}</h5>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <i class="far fa-check-circle me-2 text-success"></i>
                <span>{{ admin_lang('Configuration cache will be cleared') }}</span>
            </li>
            <li class="list-group-item">
                <i class="far fa-check-circle me-2 text-success"></i>
                <span>{{ admin_lang('Compiled views will be cleared') }}</span>
            </li>
            <li class="list-group-item">
                <i class="far fa-check-circle me-2 text-success"></i>
                <span>{{ admin_lang('Application cache will be cleared') }}</span>
            </li>
            <li class="list-group-item">
                <i class="far fa-check-circle me-2 text-success"></i>
                <span>{{ admin_lang('Route cache will be cleared') }}</span>
            </li>
            <li class="list-group-item">
                <i class="far fa-check-circle me-2 text-success"></i>
                <span>{{ admin_lang('Error logs file will be cleared') }}</span>
            </li>
            <li class="list-group-item">
                <i class="far fa-check-circle me-2 text-success"></i>
                <span>{{ admin_lang('All Other Caches will be cleared') }}</span>
            </li>
            <li class="list-group-item p-0"></li>
        </ul>
        <div class="card-body">
            <form action="{{ route('admin.systemInfo.cache') }}" method="POST" onsubmit='return confirm("{{admin_lang('Are you sure?')}}")'>
                @csrf
                <button class="btn btn-label-danger w-100"><i
                        class="icon-feather-trash-2 me-2"></i>{{ admin_lang('Clear System Cache') }}</button>
            </form>
        </div>
    </div>

    @push('scripts_at_top')
        <script type="text/javascript">
            "use strict";
            var QuickMenu = {"page": "system-info"};
        </script>
    @endpush
@endsection
