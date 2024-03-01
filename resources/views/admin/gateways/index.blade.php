@extends('admin.layouts.main')
@section('title', admin_lang('Payment Gateways'))
@section('content')

    <div class="quick-card card">
        <div class="card-body">
            <div class="dataTables_wrapper">
                <table id="basic_datatable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ admin_lang('Logo') }}</th>
                            <th>{{ admin_lang('name') }}</th>
                            <th>{{ admin_lang('Fees') }}</th>
                            <th>{{ admin_lang('Status') }}</th>
                            <th width="20" class="no-sort" data-priority="1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gateways as $gateway)
                            <tr class="item">
                                <td>
                                    <a href="{{ route('admin.gateways.edit', $gateway->id) }}">
                                        <img src="{{ asset('storage/payments/'.$gateway->logo) }}" height="35" width="100">
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.gateways.edit', $gateway->id) }}" class="text-dark">
                                        {{ $gateway->name }}
                                    </a>
                                </td>
                                <td><span class="badge bg-dark">{{ $gateway->fees }}%</span></td>
                                <td>
                                    @if ($gateway->status)
                                        <span class="badge bg-success">{{ admin_lang('Active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ admin_lang('Disabled') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex">
                                    <a href="#" data-url="{{ route('admin.gateways.edit', $gateway->id) }}" data-toggle="slidePanel" title="{{admin_lang('Edit')}}" data-tippy-placement="top" class="btn btn-default btn-icon"><i class="icon-feather-edit"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('scripts_at_top')
        <script id="quick-sidebar-menu-js-extra">
            "use strict";
            var QuickMenu = {"page": "gateways"};
        </script>
    @endpush
@endsection
