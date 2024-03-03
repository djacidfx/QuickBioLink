@extends('admin.layouts.main')
@section('title', request()->ip . ' '. lang('logs'))
@section('content')
        <div class="card">
            <div class="card-body">
                <div class="dataTables_wrapper">
                    <table id="ajax_datatable" class="table table-striped" data-jsonfile="{{ route('admin.users.logsbyip', $ip) }}">
                        <thead>
                        <tr>
                            <th>{{ lang('IP') }}</th>
                            <th class="no-sort">{{ lang('User') }}</th>
                            <th>{{ lang('Browser') }}</th>
                            <th>{{ lang('OS') }}</th>
                            <th>{{ lang('Location') }}</th>
                            <th>{{ lang('Timezone') }}</th>
                            <th>{{ lang('Latitude') }}</th>
                            <th>{{ lang('Longitude') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endsection
