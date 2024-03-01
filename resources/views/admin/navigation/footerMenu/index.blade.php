@extends('admin.layouts.main')
@section('title', admin_lang('Footer Menu').' - '.$active)

@section('header_buttons')
    @include('admin.includes.language')
    <a href="#" data-url="{{route('admin.footerMenu.create')}}" data-toggle="slidePanel" class="btn btn-info ms-2">{{ admin_lang('Add New') }}</a>
    <button form="quick-submitted-form" class="btn btn-primary ms-2 {{$footerMenuLinks->count() == 0 ? 'disabled' : ''}}">{{ admin_lang('Save') }}</button>
@endsection

@section('content')
    @if ($footerMenuLinks->count() > 0)
        <form id="quick-submitted-form" action="{{ route('admin.footerMenu.nestable') }}" method="POST">
            @csrf
            <input name="ids" id="ids" hidden>
        </form>
        <div class="quick-card card">
            <div class="dd nestable">
                <ol class="dd-list">
                    @foreach ($footerMenuLinks as $footerMenuLink)
                        <li class="dd-item" data-id="{{ $footerMenuLink->id }}">
                            <div class="dd-handle">
                                <span class="drag-indicator"></span>
                                <span class="dd-title">{{ $footerMenuLink->name }}</span>
                                <div class="dd-nodrag ms-auto d-flex">
                                    <a href="#" data-url="{{ route('admin.footerMenu.edit', $footerMenuLink->id) }}" data-toggle="slidePanel" title="{{ admin_lang('Edit') }}" data-tippy-placement="top"
                                        class="btn btn-default btn-icon me-2"><i class="icon-feather-edit"></i></a>
                                    <form class="d-inline"
                                        action="{{ route('admin.footerMenu.destroy', $footerMenuLink->id) }}"
                                        method="POST" onsubmit='return confirm("{{admin_lang('Are you sure?')}}")'>
                                        @method('DELETE')
                                        @csrf
                                        <button class="btn btn-icon btn-danger"><i
                                                class="icon-feather-trash-2"></i></button>
                                    </form>
                                </div>
                            </div>
                            @if (count($footerMenuLink->children))
                                <ol class="dd-list">
                                    @foreach ($footerMenuLink->children as $child)
                                        <li class="dd-item" data-id="{{ $child->id }}">
                                            <div class="dd-handle">
                                                <span class="drag-indicator"></span>
                                                <span class="dd-title">{{ $child->name }}</span>
                                                <div class="dd-nodrag ms-auto d-flex">
                                                    <a href="#" data-url="{{ route('admin.footerMenu.edit', $child->id) }}" data-toggle="slidePanel" title="{{ admin_lang('Edit') }}" data-tippy-placement="top"
                                                        class="btn btn-default btn-icon me-1"><i class="icon-feather-edit"></i></a>
                                                    <form class="d-inline"
                                                        action="{{ route('admin.footerMenu.destroy', $child->id) }}"
                                                        method="POST" onsubmit='return confirm("{{admin_lang('Are you sure?')}}")'>
                                                        @method('DELETE')
                                                        @csrf
                                                        <button class="btn btn-icon btn-danger"><i
                                                                class="icon-feather-trash-2"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    @else
        <div class="quick-card card">
            <div class="card-body">
                @include('admin.includes.empty')
            </div>
        </div>
    @endif
    @push('scripts_at_top')
        <script type="text/javascript">
            "use strict";
            var QuickMenu = {"page": "navigation", "subpage": "footerMenu"};
        </script>
    @endpush
    @if ($footerMenuLinks->count() > 0)
        @push('styles_vendor')
            <link rel="stylesheet" href="{{ asset('admin/assets/plugins/nestable/jquery.nestable.min.css') }}">
        @endpush
        @push('scripts_vendor')
            <script src="{{ asset('admin/assets/plugins/nestable/jquery.nestable.min.js') }}"></script>
        @endpush
    @endif
@endsection
