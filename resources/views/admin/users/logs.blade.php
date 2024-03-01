@extends('admin.layouts.main')
@section('title', request()->ip . ' '. admin_lang('logs'))
@section('content')
        <div class="card">
            <div class="card-body">
                <div class="dataTables_wrapper">
                    <table id="ajax_datatable" class="table table-striped" data-jsonfile="{{ route('admin.users.logsbyip', $ip) }}">
                        <thead>
                        <tr>
                            <th>{{ admin_lang('IP') }}</th>
                            <th class="no-sort">{{ admin_lang('User') }}</th>
                            <th>{{ admin_lang('Browser') }}</th>
                            <th>{{ admin_lang('OS') }}</th>
                            <th>{{ admin_lang('Location') }}</th>
                            <th>{{ admin_lang('Timezone') }}</th>
                            <th>{{ admin_lang('Latitude') }}</th>
                            <th>{{ admin_lang('Longitude') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endsection
