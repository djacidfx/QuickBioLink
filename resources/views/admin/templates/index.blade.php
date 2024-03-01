@extends('admin.layouts.main')
@section('title', admin_lang('Templates'))
@section('content')
    <div class="row">

        @foreach($templates as $temp)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header"><h5>{{ ucfirst($temp['name']) }}</h5></div>
                    <div class="card-body">
                        <img src="{{ $temp['image'] }}" class="w-100 mb-4 border-label-secondary rounded border-1">
                        @if(active_theme_name() == $temp['name'])
                            <button type="submit" name="name" value="{{ $temp['name'] }}" class="btn btn-label-secondary" disabled>{{admin_lang('Activated')}}</button>
                        @else
                            <form action="" method="post">
                                @csrf
                                <button type="submit" name="name" value="{{$temp['name']}}" class="btn btn-primary">{{admin_lang('Active Me')}}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        @if($extra_templates)
            @foreach($extra_templates as $temp)
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header"><h5>{{ ucfirst($temp['name']) }}</h5></div>
                        <div class="card-body">
                            <img src="{{ $temp['image'] }}" class="w-100 mb-4 border-label-secondary rounded border-1">
                            <a href="{{$temp['url']}}" target="_blank" class="btn btn-primary">{{admin_lang('Get This')}}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>
    @push('scripts_at_top')
        <script id="quick-sidebar-menu-js-extra">
            "use strict";
            var QuickMenu = {"page": "templates"};
        </script>
    @endpush
@endsection


