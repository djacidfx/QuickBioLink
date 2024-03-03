<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo ">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
          <img width="25" src="{{ asset('storage/brand/'.$settings->media->admin_logo) }}" alt="{{ @$settings->site_title }}" />
      </span>
            <span class="app-brand-text demo menu-text fw-bold">{{ lang('Admin') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
            <i class="icon-feather-chevron-left fs-5 align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ lang('Apps') }}</span>
        </li>
        <li class="menu-item" data-page="dashboard">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon icon-feather-home"></i>
                <div class="text-truncate">{{ lang('Dashboard') }}</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ lang('MANAGEMENT') }}</span>
        </li>
        <li class="menu-item" data-page="posts">
            <a href="{{ route('admin.posts.index') }}" class="menu-link">
                <i class="menu-icon icon-feather-paperclip"></i>
                <div class="text-truncate">{{ lang('Bio Links') }}</div>
            </a>
        </li>
        <li class="menu-item" data-page="users">
            <a href="{{ route('admin.users.index') }}" class="menu-link">
                <i class="menu-icon  icon-feather-users"></i>
                <div class="text-truncate">{{ lang('Manage Users') }}</div>
                @if ($unviewedUsersCount)
                    <span class="badge rounded-pill bg-danger ms-auto">{{ $unviewedUsersCount }}</span>
                @endif
            </a>
        </li>
        <li class="menu-item" data-page="membership">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon  icon-feather-gift"></i>
                <div class="text-truncate">{{ lang('Membership') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item" data-page="membership-plans">
                    <a href="{{ route('admin.plans.index') }}" class="menu-link">
                        <div class="text-truncate">{{ lang('Membership Plans') }}</div>
                    </a>
                </li>
                <li class="menu-item" data-page="plan-options">
                    <a href="{{ route('admin.planoption.index') }}" class="menu-link">
                        <div class="text-truncate">{{ lang('Plan Options') }}</div>
                    </a>
                </li>
                <li class="menu-item" data-page="subscriptions">
                    <a href="{{ route('admin.subscriptions.index') }}" class="menu-link">
                        <div class="text-truncate">{{ lang('Subscriptions') }}</div>
                        @if ($unviewedSubscriptions)
                            <span class="badge rounded-pill bg-danger ms-auto">{{ $unviewedSubscriptions }}</span>
                        @endif
                    </a>
                </li>
                <li class="menu-item" data-page="coupons">
                    <a href="{{ route('admin.coupons.index') }}" class="menu-link">
                        <div class="text-truncate">{{ lang('Coupon Codes') }}</div>
                    </a>
                </li>
                <li class="menu-item" data-page="taxes">
                    <a href="{{ route('admin.taxes.index') }}" class="menu-link">
                        <div class="text-truncate">{{ lang('Taxes') }}</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item" data-page="gateways">
            <a href="{{ route('admin.gateways.index') }}" class="menu-link">
                <i class="menu-icon  icon-feather-server"></i>
                <div class="text-truncate">{{ lang('Payment Gateways') }}</div>
            </a>
        </li>
        <li class="menu-item" data-page="transactions">
            <a href="{{ route('admin.transactions.index') }}" class="menu-link">
                <i class="menu-icon  icon-feather-trending-up"></i>
                <div class="text-truncate">{{ lang('Transactions') }}</div>
                @if ($unviewedTransactionsCount)
                    <span class="badge rounded-pill bg-danger ms-auto">{{ $unviewedTransactionsCount }}</span>
                @endif
            </a>
        </li>
        <li class="menu-item" data-page="email-template">
            <a href="{{ route('admin.mailtemplates.index') }}" class="menu-link">
                <i class="menu-icon  icon-feather-mail"></i>
                <div class="text-truncate">{{ lang('Email Templates') }}</div>
            </a>
        </li>
        <li class="menu-item" data-page="languages">
            <a href="{{ route('admin.languages.index') }}" class="menu-link">
                <i class="menu-icon  icon-feather-globe"></i>
                <div class="text-truncate">{{ lang('Languages') }}</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ lang('CONTENT') }}</span>
        </li>
        @if ($settings->blog->status)
        <li class="menu-item" data-page="blog">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon  icon-feather-file-text"></i>
                <div class="text-truncate">{{ lang('Blog') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item" data-page="all-blogs">
                    <a href="{{ route('admin.articles.index') }}" class="menu-link">
                        <div class="text-truncate">{{ lang('Blogs') }}</div>
                    </a>
                </li>
                <li class="menu-item" data-page="blog-post">
                    <a href="{{ route('admin.articles.create') }}" class="menu-link">
                        <div class="text-truncate">{{lang('Add New')}}</div>
                    </a>
                </li>
                <li class="menu-item" data-page="blog-cat">
                    <a href="{{ route('admin.categories.index') }}" class="menu-link">
                        <div class="text-truncate">{{ lang('Categories') }}</div>
                    </a>
                </li>
                <li class="menu-item" data-page="blog-comments">
                    <a href="{{ route('admin.comments.index') }}" class="menu-link">
                        <div class="text-truncate">{{ lang('Comments') }}</div>
                        @if ($commentsNeedsAction)
                            <span class="badge rounded-pill bg-danger ms-auto">{{ $commentsNeedsAction }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </li>
        @endif
        <li class="menu-item" data-page="testimonials">
            <a href="{{ route('admin.testimonials.index') }}" class="menu-link">
                <i class="menu-icon  icon-feather-star"></i>
                <div class="text-truncate">{{ lang('Testimonials') }}</div>
            </a>
        </li>
        <li class="menu-item" data-page="pages">
            <a href="{{ route('admin.pages.index') }}" class="menu-link">
                <i class="menu-icon  icon-feather-file"></i>
                <div class="text-truncate">{{ lang('Pages') }}</div>
            </a>
        </li>
        <li class="menu-item" data-page="faqs">
            <a href="{{ route('admin.faqs.index') }}" class="menu-link">
                <i class="menu-icon icon-feather-file-text"></i>
                <div class="text-truncate">{{lang('FAQs')}}</div>
            </a>
        </li>
        <li class="menu-item" data-page="navigation">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon  icon-feather-list"></i>
                <div class="text-truncate">{{ lang('Menu') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item" data-page="navbarMenu">
                    <a href="{{ route('admin.navbarMenu.index') }}" class="menu-link">
                        <div class="text-truncate">{{ lang('Navbar Menu') }}</div>
                    </a>
                </li>
                <li class="menu-item" data-page="footerMenu">
                    <a href="{{ route('admin.footerMenu.index') }}" class="menu-link">
                        <div class="text-truncate">{{ lang('Footer Menu') }}</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item" data-page="advertisements">
            <a href="{{ route('admin.advertisements.index') }}" class="menu-link">
                <i class="menu-icon  icon-feather-monitor"></i>
                <div class="text-truncate">{{lang('Advertisements')}}</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ lang('Settings') }}</span>
        </li>
        <li class="menu-item" data-page="settings">
            <a href="{{ route('admin.settings.index') }}" class="menu-link">
                <i class="menu-icon  icon-feather-settings"></i>
                <div class="text-truncate">{{ lang('Settings') }}</div>
            </a>
        </li>
        <li class="menu-item" data-page="templates">
            <a href="{{ route('admin.templates.index') }}" class="menu-link">
                <i class="menu-icon  icon-feather-tablet"></i>
                <div class="text-truncate">{{lang('Templates')}}</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ lang('Account') }}</span>
        </li>
        <li class="menu-item" data-page="logout">
            <a href="javascript:void(0);" class="menu-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="menu-icon  icon-feather-log-out"></i>
                <div class="text-truncate">{{lang('Logout')}}</div>
            </a>
            <form id="logout-form" class="d-inline" action="{{ route('logout') }}" method="POST">
                @csrf
            </form>
        </li>
        <li class="menu-header hide-arrow small">
            <span class="menu-header-text">{{lang('Version')}} {{env('APP_VERSION')}}</span>
        </li>
    </ul>
</aside>

