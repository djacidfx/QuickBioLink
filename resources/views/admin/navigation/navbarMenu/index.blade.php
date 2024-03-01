@extends('admin.layouts.main')
@section('title', admin_lang('Navbar Menu').' - '.$active)

@section('header_buttons')
    @include('admin.includes.language')
    <a href="#" data-url="{{route('admin.navbarMenu.create')}}" data-toggle="slidePanel" class="btn btn-info ms-2">{{ admin_lang('Add New') }}</a>
    <button form="quick-submitted-form" class="btn btn-primary ms-2 {{$navbarMenuLinks->count() == 0 ? 'disabled' : ''}}">{{ admin_lang('Save') }}</button>
@endsection

@section('content')
    @if ($navbarMenuLinks->count() > 0)
        <form id="quick-submitted-form" action="{{ route('admin.navbarMenu.nestable') }}" method="POST">
            @csrf
            <input name="ids" id="ids" hidden>
        </form>
        <div class="card overflow-hidden">
            <div class="dd nestable">
                <ol class="dd-list">
                    @foreach ($navbarMenuLinks as $navbarMenuLink)
                        <li class="dd-item" data-id="{{ $navbarMenuLink->id }}">
                            <div class="dd-handle">
                                <span class="drag-indicator"></span>
                                <span class="dd-title">{{ $navbarMenuLink->name }}</span>
                                <div class="dd-nodrag ms-auto d-flex">
                                    <a href="#" data-url="{{ route('admin.navbarMenu.edit', $navbarMenuLink->id) }}" data-toggle="slidePanel" title="{{ admin_lang('Edit') }}" data-tippy-placement="top"
                                        class="btn btn-default btn-icon me-1"><i class="icon-feather-edit"></i></a>
                                    <form class="d-inline"
                                        action="{{ route('admin.navbarMenu.destroy', $navbarMenuLink->id) }}"
                                        method="POST" onsubmit='return confirm("{{admin_lang('Are you sure?')}}")'>
                                        @method('DELETE')
                                        @csrf
                                        <button class="btn btn-icon btn-danger"><i class="icon-feather-trash-2"></i></button>
                                    </form>
                                </div>
                            </div>
                            @if (count($navbarMenuLink->children))
                                <ol class="dd-list">
                                    @foreach ($navbarMenuLink->children as $child)
                                        <li class="dd-item" data-id="{{ $child->id }}">
                                            <div class="dd-handle">
                                                <span class="drag-indicator"></span>
                                                <span class="dd-title">{{ $child->name }}</span>
                                                <div class="dd-nodrag ms-auto d-flex">
                                                    <a href="#" data-url="{{ route('admin.navbarMenu.edit', $child->id) }}" data-toggle="slidePanel" title="{{ admin_lang('Edit') }}" data-tippy-placement="top"
                                                        class="btn btn-default btn-icon me-2"><i class="icon-feather-edit"></i></a>
                                                    <form class="d-inline"
                                                        action="{{ route('admin.navbarMenu.destroy', $child->id) }}"
                                                        method="POST" onsubmit='return confirm("{{admin_lang('Are you sure?')}}")'>
                                                        @method('DELETE')
                                                        @csrf
                                                        <button class="btn btn-icon btn-danger"><i class="icon-feather-trash-2"></i></button>
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
            var QuickMenu = {"page": "navigation", "subpage": "navbarMenu"};
        </script>
    @endpush
    @if ($navbarMenuLinks->count() > 0)
        @push('styles_vendor')
            <link rel="stylesheet" href="{{ asset('admin/assets/plugins/nestable/jquery.nestable.min.css') }}">
        @endpush
        @push('scripts_vendor')
            <script src="{{ asset('admin/assets/plugins/nestable/jquery.nestable.min.js') }}"></script>
        @endpush
    @endif
@endsection
