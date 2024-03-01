@extends('admin.layouts.main')
@section('title', admin_lang('Languages'))
@section('header_buttons')
    <a href="#" data-url="{{ route('admin.languages.create') }}" data-toggle="slidePanel" class="btn btn-primary ms-2"><i class="icon-feather-plus me-2"></i> {{ admin_lang('Add New') }}</a>
@endsection
@section('content')
    <div class="quick-card card">
        <div class="card-body">
            <div class="dataTables_wrapper">
                <table class="table table-striped" id="ajax_datatable" data-jsonfile="{{ route('admin.languages.index') }}" data-reorder-route="{{ route('admin.languages.reorder') }}" data-order-dir="asc">
                    <thead>
                    <tr>
                        <th width="20"></th>
                        <th>{{ admin_lang('Name') }}</th>
                        <th>{{ admin_lang('Status') }}</th>
                        <th width="20" class="no-sort" data-priority="1"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- Site Action -->
    <div class="site-action">
        <button type="button" class="front-icon btn btn-primary btn-floating"
                data-url="{{ route('admin.languages.create') }}" data-toggle="slidePanel">
            <i class="icon-feather-plus animation-scale-up" aria-hidden="true"></i>
        </button>
        <button type="button" class="back-icon btn btn-primary btn-floating">
            <i class="icon-feather-x animation-scale-up" aria-hidden="true"></i>
        </button>
    </div>

    @push('scripts_at_top')
        <script id="quick-sidebar-menu-js-extra">
            "use strict";
            var QuickMenu = {"page": "languages"};
        </script>
    @endpush
@endsection
