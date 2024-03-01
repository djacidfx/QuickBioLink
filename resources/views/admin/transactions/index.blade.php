@extends('admin.layouts.main')
@section('title', admin_lang('Transactions'))
@section('content')
    <div class="quick-card card">
        <div class="card-body">
            <div class="dataTables_wrapper">
                <table class="table table-striped" id="ajax_datatable" data-jsonfile="{{ route('admin.transactions.index') }}">
                    <thead>
                    <tr>
                        <th class="tb-w-2x">{{ admin_lang('#ID') }}</th>
                        <th class="tb-w-7x">{{ admin_lang('User') }}</th>
                        <th class="tb-w-3x">{{ admin_lang('Plan') }}</th>
                        <th class="tb-w-3x">{{ admin_lang('Total') }}</th>
                        <th class="tb-w-3x">{{ admin_lang('Gateway') }}</th>
                        <th class="tb-w-3x">{{ admin_lang('Type') }}</th>
                        <th class="tb-w-3x">{{ admin_lang('Status') }}</th>
                        <th class="tb-w-7x">{{ admin_lang('Created at') }}</th>
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
            <button type="button" id="quick-delete-button" data-action="{{ route('admin.transactions.delete') }}"
                    class="btn btn-danger btn-floating animation-slide-bottom">
                <i class="icon icon-feather-trash-2" aria-hidden="true"></i>
            </button>
        </div>
        <button type="button" class="back-icon btn btn-primary btn-floating">
            <i class="icon-feather-x animation-scale-up" aria-hidden="true"></i>
        </button>
    </div>
    @push('scripts_at_top')
        <script type="text/javascript">
            "use strict";
            var QuickMenu = {"page": "transactions"};
        </script>
    @endpush
@endsection
