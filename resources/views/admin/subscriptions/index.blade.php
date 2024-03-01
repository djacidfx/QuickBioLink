@extends('admin.layouts.main')
@section('title', admin_lang('Subscriptions'))
@section('header_buttons')
    <a href="#" data-url="{{ route('admin.subscriptions.create') }}" data-toggle="slidePanel" class="btn btn-primary ms-2"><i class="icon-feather-plus me-2"></i> {{ admin_lang('Add New') }}</a>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-success">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="fas fa-users"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ number_format($activeSubscriptions->count()) }}</h4>
                    </div>
                    <p class="mb-0 fs-6">{{ admin_lang('Active Subscriptions') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-danger"><i class="fas fa-user-clock"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ number_format($expiredSubscriptions->count()) }}</h4>
                    </div>
                    <p class="mb-0 fs-6">{{ admin_lang('Expired Subscriptions') }}</p>
                </div>
            </div>
        </div>
        @if ($canceledSubscriptions->count() > 0)
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-warning"><i
                                    class="fas fa-user-times"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ number_format($canceledSubscriptions->count() ) }}</h4>
                    </div>
                    <p class="mb-0 fs-6">{{ admin_lang('Canceled Subscriptions') }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="quick-card card">
        <div class="card-body">
            <div class="dataTables_wrapper">
                <table class="table table-striped" id="ajax_datatable" data-jsonfile="{{ route('admin.subscriptions.index') }}">
                    <thead>
                    <tr>
                        <th>{{ admin_lang('#') }}</th>
                        <th>{{ admin_lang('User details') }}</th>
                        <th>{{ admin_lang('Plan') }}</th>
                        <th>{{ admin_lang('Subscribe at') }}</th>
                        <th>{{ admin_lang('Expiring at') }}</th>
                        <th>{{ admin_lang('Status') }}</th>
                        <th width="20" class="no-sort" data-priority="1"></th>
                        <th width="20" class="no-sort" data-priority="1">
                            <div class="checkbox">
                                <input type="checkbox" id="quick-checkbox-all">
                                <label for="quick-checkbox-all"><span class="checkbox-icon"></span></label>
                            </div>
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Site Action -->
    <div class="site-action">
        <div class="site-action-buttons">
            <button type="button" id="quick-delete-button" data-action="{{ route('admin.subscriptions.delete') }}"
                    class="btn btn-danger btn-floating animation-slide-bottom">
                <i class="icon icon-feather-trash-2" aria-hidden="true"></i>
            </button>
        </div>
        <button type="button" class="front-icon btn btn-primary btn-floating"
                data-url="{{ route('admin.subscriptions.create') }}" data-toggle="slidePanel">
            <i class="icon-feather-plus animation-scale-up" aria-hidden="true"></i>
        </button>
        <button type="button" class="back-icon btn btn-primary btn-floating">
            <i class="icon-feather-x animation-scale-up" aria-hidden="true"></i>
        </button>
    </div>
    @push('scripts_at_top')
        <script type="text/javascript">
            "use strict";
            var QuickMenu = {"page": "membership", "subpage": "subscriptions"};
        </script>
    @endpush
@endsection
