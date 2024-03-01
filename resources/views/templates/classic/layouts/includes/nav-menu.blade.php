<ul class="navbar-nav">
    @foreach ($navbarMenuLinks as $navbarMenuLink)
        @php
            if (!filter_var($navbarMenuLink->link, FILTER_VALIDATE_URL)) {
                $navbarMenuLink->link = url("/").$navbarMenuLink->link;
            }
        @endphp
        @if ($navbarMenuLink->children->count() > 0)
            <li class="nav-item">
                <a href="{{ $navbarMenuLink->link }}" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    {{ $navbarMenuLink->name }}
                </a>
                <ul class="dropdown-menu">
                    @foreach ($navbarMenuLink->children as $child)
                        @php
                            if (!filter_var($child->link, FILTER_VALIDATE_URL)) {
                                $child->link = url("/").$child->link;
                            }
                        @endphp
                        <li class="nav-item">
                            <a href="{{ $child->link }}" class="nav-link">{{ $child->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @else
            <li class="nav-item">
                <a href="{{ $navbarMenuLink->link }}" class="nav-link">
                    {{ $navbarMenuLink->name }}
                </a>
            </li>
        @endif
    @endforeach

    @guest
        @if ($settings->enable_user_registration)
        <!--/ # When user logout or new user login signup button-->
        <li class="nav-item d-block d-sm-none">
            <a href="{{ route('login') }}" class="nav-link">
                {{ lang('Log in', 'auth') }}
            </a>
        </li>
        <li class="nav-item d-block d-sm-none">
            <a href="{{ route('register') }}" class="nav-link">
                {{ lang('Sign up', 'auth') }}
            </a>
        </li>
        <!--/ # When user logout or new user login signup button-->
        @endif
    @endguest
</ul>
