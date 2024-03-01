@extends('admin.layouts.main')
@section('title', admin_lang('FAQs') . ' - '.$active)
@section('header_buttons')
    @include('admin.includes.language')
    <a href="#" data-url="{{ route('admin.faqs.create') }}" data-toggle="slidePanel" class="btn btn-primary ms-2"><i class="icon-feather-plus me-2"></i> {{ admin_lang('Add New') }}</a>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
        <div class="dataTables_wrapper">
            <table id="basic_datatable" class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ admin_lang('Language') }}</th>
                    <th>{{ admin_lang('Title') }}</th>
                    <th>{{ admin_lang('Published date') }}</th>
                    <th width="20" class="no-sort"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($faqs as $faq)
                    <tr class="item">
                        <td>{{ $faq->id }}</td>
                        <td><span class="text-uppercase">{{ $faq->language->code }}</span></td>
                        <td>{{ text_shorting($faq->title, 40) }}</td>
                        <td>{{ date_formating($faq->created_at) }}</td>
                        <td>
                            <div class="d-flex">
                                <a href="#" data-url="{{ route('admin.faqs.edit', $faq->id) }}" data-toggle="slidePanel" title="{{ admin_lang('Edit') }}" class="btn btn-default btn-icon me-2" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                                <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" onsubmit='return confirm("{{admin_lang('Are you sure?')}}")'>
                                    @csrf @method('DELETE')
                                    <button class="btn btn-icon btn-danger" title="{{ admin_lang('Delete') }}" data-tippy-placement="top"><i class="icon-feather-trash-2"></i></button>
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
                data-url="{{ route('admin.faqs.create') }}" data-toggle="slidePanel">
            <i class="icon-feather-plus animation-scale-up" aria-hidden="true"></i>
        </button>
        <button type="button" class="back-icon btn btn-primary btn-floating">
            <i class="icon-feather-x animation-scale-up" aria-hidden="true"></i>
        </button>
    </div>
    @push('scripts_at_top')
        <script type="text/javascript">
            "use strict";
            var QuickMenu = {"page": "faqs"};
        </script>
    @endpush
    @push('scripts_vendor')
        <script src="{{ asset('admin/assets/plugins/tinymce/tinymce.min.js') }}"></script>
    @endpush
@endsection
