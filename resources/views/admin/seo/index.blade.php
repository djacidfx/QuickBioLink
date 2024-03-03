@extends('admin.layouts.main')
@section('title', lang('SEO Configurations'))
@section('header_buttons')
    <a href="#" data-url="{{ route('admin.seo.create') }}" data-toggle="slidePanel" class="btn btn-primary ms-2"><i class="icon-feather-plus me-2"></i> {{ lang('Add New') }}</a>
@endsection
@section('content')
    <div class="alert d-flex align-items-center bg-label-info mb-3" role="alert">
        <span class="badge badge-center rounded-pill bg-info border-label-info p-3 me-2"><i class="fas fa-bell"></i></span>
        <div class="ps-1">
            <span>{{lang("Here you can setup your site's seo details for multiple languages.")}}</span>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="dataTables_wrapper">
                <table id="basic_datatable" class="table table-striped">
                    <thead>
                        <tr>
                            <th class="tb-w-3x">{{ lang('Language') }}</th>
                            <th class="tb-w-20x">{{ lang('Site title') }}</th>
                            <th class="tb-w-7x">{{ lang('Last Updated') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($configurations as $configuration)
                            <tr class="item">
                                <td><span class="text-uppercase">{{ $configuration->language->code }}</span></td>
                                <td>{{ text_shorting($configuration->title, 60) }}</td>
                                <td>{{ date_formating($configuration->updated_at) }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="#" data-url="{{ route('admin.seo.edit', $configuration->id) }}" data-toggle="slidePanel" title="{{lang('Edit')}}" data-tippy-placement="top" class="btn btn-default btn-icon me-1"><i class="icon-feather-edit"></i></a>
                                        <form class="d-inline" action="{{ route('admin.seo.destroy', $configuration->id) }}" method="POST" onsubmit='return confirm("{{lang('Are you sure?')}}")'>
                                            @method('DELETE')
                                            @csrf
                                            <button class="btn btn-icon btn-danger" title="{{lang('Delete')}}" data-tippy-placement="top"><i
                                                    class="icon-feather-trash-2"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Site Action -->
    <div class="site-action">
        <button type="button" class="front-icon btn btn-primary btn-floating"
                data-url="{{ route('admin.seo.create') }}" data-toggle="slidePanel">
            <i class="icon-feather-plus animation-scale-up" aria-hidden="true"></i>
        </button>
        <button type="button" class="back-icon btn btn-primary btn-floating">
            <i class="icon-feather-x animation-scale-up" aria-hidden="true"></i>
        </button>
    </div>
    @push('scripts_at_top')
        <script id="quick-sidebar-menu-js-extra">
            "use strict";
            var QuickMenu = {"page": "seo"};
        </script>
    @endpush
@endsection
