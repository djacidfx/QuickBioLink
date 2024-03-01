<div class="nav-others d-flex align-items-center">
    <!--/ # Change language button-->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a title="{{ get_lang_name() }}" href="#" class="nav-link dropdown-toggle d-flex align-items-center">
                <img src="{{ get_lang_flag() }}" title="{{ get_lang_name() }}" alt="{{ get_lang_name() }}" width="16" height="11">
                <span class="ml-5 d-none d-sm-block">{{ get_lang_name() }}</span>
            </a>
            <ul class="dropdown-menu w-auto">
                @foreach ($languages as $language)
                    <li class="nav-item">
                        <a title="English" href="{{ lang_url($language->code) }}" class="nav-link @if ($language->code == get_lang()) active @endif">
                            <img src="{{ asset('storage/flags/'.$language->flag) }}" alt="{{ $language->name }}" title="{{ $language->name }}" width="16" height="11">
                            <span class="ml-5">{{ $language->name }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    </ul>
    <!--/ # Change language button-->
@auth
    <!--/ # When user login user profile image with button-->
        <div class="dropdown ml-16 ">
            <button class="button icon-group -secondary dropdown-toggle size-45" type="button"
                    data-bs-toggle="dropdown">
                <img src="{{ asset('storage/avatars/users/'.user_auth_info()->avatar) }}" alt="{{ user_auth_info()->name }}" class="rounded-circle">
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @if(user_auth_info()->user_type == 'admin')
                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}" target="_blank"><i class="fa-regular fa-user-tie-hair mr-5"></i> {{ lang('Admin', 'account') }}</a></li>
                    <div class="dropdown-divider"></div>
                @endif
                <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fa-regular fa-home mr-5"></i> {{ lang('Dashboard', 'account') }}</a></li>
                <li><a class="dropdown-item" href="{{ route('subscription') }}"><i class="fa-regular fa-gem mr-5"></i> {{ lang('My Subscription', 'account') }}</a></li>
                <li><a class="dropdown-item" href="{{ route('transactions') }}"><i class="fa-regular fa-receipt mr-5"></i> {{ lang('Transactions', 'account') }}</a></li>
                <div class="dropdown-divider"></div>
                <li><a class="dropdown-item" href="{{ route('settings') }}"><i class="fa-regular fa-cog mr-5"></i> {{ lang('Settings', 'account') }}</a></li>
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa-regular fa-right-from-bracket mr-5"></i> {{ lang('Logout', 'auth') }}</a></li>
            </ul>
        </div>
        <form id="logout-form" class="d-inline" action="{{ route('logout') }}" method="POST">
            @csrf
        </form>
        <!--/ # When user login user profile image with button-->
@endauth
@guest
    @if ($settings->enable_user_registration)
        <!--/ # When user logout or new user login signup button-->
            <div class="d-flex justify-content-center d-none d-md-flex">
                <a href="{{ route('login') }}"
                   class="ml-16 button -secondary text-dark-1 px-15 rounded-pill fw-semibold font-16">{{ lang('Log in', 'auth') }}
                </a>
                <a href="{{ route('register') }}"
                   class="ml-16 button bg-primary text-white px-15 rounded-pill fw-semibold font-16">{{ lang('Sign up', 'auth') }}
                </a>
            </div>
            <!--/ # When user logout or new user login signup button-->
    @endif
@endguest

<!--/ # On responsive hamburger menu button for offcanvas desktop nav-->
    <div class="sidemenu-header ml-16 d-lg-none">
        <div class="responsive-burger-menu icon-group -secondary" data-bs-toggle="offcanvas"
             data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
            <i class="fa-solid fa-bars-staggered"></i>
        </div>
    </div>
    <!--/ # On responsive hamburger menu button for offcanvas desktop nav-->
</div>
