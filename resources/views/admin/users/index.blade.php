@extends('admin.layouts.main')
@section('title', admin_lang('Users'))
@section('header_buttons')
    <a href="#" data-url="{{ route('admin.users.create') }}" data-toggle="slidePanel" class="btn btn-primary ms-2"><i class="icon-feather-plus me-2"></i> {{ admin_lang('Add New') }}</a>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="fas fa-users"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ number_format($activeUsersCount) }}</h4>
                    </div>
                    <p class="mb-0 fs-6">{{ admin_lang('Active Users') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-danger"><i class="fas fa-user-plus"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ number_format($bannedUserscount) }}</h4>
                    </div>
                    <p class="mb-0 fs-6">{{ admin_lang('Banned Users') }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="quick-card card">
        <div class="card-body">
            <div class="dataTables_wrapper">
                <table class="table table-striped" id="ajax_datatable" data-jsonfile="{{ route('admin.users.index') }}">
                    <thead>
                    <tr>
                        <th data-priority="1">#</th>
                        <th data-priority="1">{{ admin_lang('User details') }}</th>
                        <th>{{ admin_lang('Email') }}</th>
                        <th class="no-sort">{{ admin_lang('Subscription') }}</th>
                        <th>{{ admin_lang('Email status') }}</th>
                        <th>{{ admin_lang('Account status') }}</th>
                        <th>{{ admin_lang('Registered date') }}</th>
                        <th class="no-sort w-px-20" data-priority="1"></th>
                        <th class="no-sort w-px-20" data-priority="1">
                            <div class="checkbox">
                                <input type="checkbox" id="quick-checkbox-all">
                                <label for="quick-checkbox-all"><span class="checkbox-icon"></span></label>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Site Action -->
    <div class="site-action">
        <div class="site-action-buttons">
            <button type="button" id="quick-delete-button" data-action="{{ route('admin.users.delete') }}"
                    class="btn btn-danger btn-floating animation-slide-bottom">
                <i class="icon icon-feather-trash-2" aria-hidden="true"></i>
            </button>
        </div>
        <button type="button" class="front-icon btn btn-primary btn-floating"
                data-url="{{ route('admin.users.create') }}" data-toggle="slidePanel">
            <i class="icon-feather-plus animation-scale-up" aria-hidden="true"></i>
        </button>
        <button type="button" class="back-icon btn btn-primary btn-floating">
            <i class="icon-feather-x animation-scale-up" aria-hidden="true"></i>
        </button>
    </div>
    @push('scripts_at_top')
        <script type="text/javascript">
            "use strict";
            var QuickMenu = {"page": "users"};
        </script>
    @endpush
@endsection
