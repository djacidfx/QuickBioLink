<!DOCTYPE html>
<html lang="{{ get_lang() }}">
<head>
    @include($activeTheme.'layouts.includes.head')
    @include($activeTheme.'layouts.includes.styles')
    {!! head_code() !!}
</head>
<body>
@php
    $navclass = $__env->yieldContent('navclass') ?: 'nav-light';
@endphp
<div class="navbar-area position-absolute {{ $navclass }}">
    @include($activeTheme.'layouts.includes.nav-main')
</div>
@yield('content')
@include($activeTheme.'layouts.includes.footer')
@include($activeTheme.'layouts.includes.widgets')
@include($activeTheme.'layouts.includes.scripts')
</body>
</html>
