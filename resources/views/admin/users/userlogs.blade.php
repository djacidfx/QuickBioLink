@extends('admin.layouts.main')
@section('title', 'Login logs')
@section('content')
    <div class="row">
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            @include('admin.users.userdetails')
        </div>
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item"><a class="nav-link " href="{{ route('admin.users.edit', $user->id) }}"><i class="icon-feather-user me-2"></i>{{ lang('Account details') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.password', $user->id) }}"><i
                            class="icon-feather-lock me-2"></i>{{ lang('Password') }}</a></li>
                <li class="nav-item"><a class="nav-link active" href="#"><i class="icon-feather-list me-2"></i>{{ lang('Login logs') }}</a></li>
            </ul>
            <div class="card">
                <div class="card-body">
                    <div class="dataTables_wrapper">
                        <table id="ajax_datatable" class="table table-striped" data-jsonfile="{{ route('admin.users.logs', $user->id) }}">
                            <thead>
                            <tr>
                                <th>{{ lang('IP') }}</th>
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
        </div>
    </div>
    @push('scripts_at_top')
        <script type="text/javascript">
            "use strict";
            var QuickMenu = {"page": "users"};
        </script>
    @endpush
@endsection
