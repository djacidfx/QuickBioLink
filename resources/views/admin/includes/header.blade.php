<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0  d-xl-none ">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="icon-feather-menu fs-4"></i>
        </a>
    </div>
    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <div class="navbar-nav align-items-center app-brand">
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-none d-xl-block left-0">
                <i class="icon-feather-chevron-left fs-5 align-middle"></i>
            </a>
        </div>
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <li class="nav-item me-3 me-xl-0"><a class="nav-link" href="{{ url('/') }}" title="{{ lang('Frontend') }}" data-tippy-placement="top" target="_blank"><i class="icon-feather-external-link fs-5"></i></a></li>
            <li class="nav-item me-3 me-xl-0"><a class="nav-link" href="#" onclick="toggleFullScreen()" title="{{ lang('Full Screen') }}" data-tippy-placement="top"><i class="icon-feather-maximize fs-5"></i></a></li>
            <!-- Notification -->
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="{{ lang('Notifications') }}" data-tippy-placement="top">
                    <i class="icon-feather-bell fs-5"></i>
                    <span class="badge bg-danger rounded-pill badge-notifications">{{ $unreadAdminNotifications }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0">
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h6 class="mb-0 me-auto">{{ lang('Notifications') }} ({{ $unreadAdminNotificationsAll }})</h6>
                            @if ($unreadAdminNotifications)
                                <a href="{{ route('admin.notifications.markasread') }}" title="{{ lang('Mark All as Read') }}" data-tippy-placement="top" class="text-body"><i class="far fa-envelope-open fs-5"></i></a>
                            @endif
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container">
                        <ul class="list-group list-group-flush">
                            @forelse ($adminNotifications as $adminNotification)
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    @if ($adminNotification->link)
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar">
                                                        <a href="{{ route('admin.notifications.view', hashid($adminNotification->id)) }}">
                                                            @if ($adminNotification->type == 'new_user')
                                                                <span class="avatar-initial rounded bg-label-success"><i
                                                                        class="fas fa-user-plus"></i></span>
                                                            @elseif ($adminNotification->type == 'new_comment')
                                                                <span class="avatar-initial rounded bg-label-warning"><i
                                                                        class="fas fa-comment"></i></span>
                                                            @endif
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="{{ route('admin.notifications.view', hashid($adminNotification->id)) }}">
                                                    <h6 class="mb-1" title="{{ $adminNotification->title }}">{{ text_shorting($adminNotification->title, 30) }}
                                                        ️</h6>
                                                    <small
                                                        class="text-muted">{{ $adminNotification->created_at->diffforhumans() }}</small>
                                                    </a>
                                                </div>
                                                @if (!$adminNotification->status)
                                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                                        <span class="dropdown-notifications-read">
                                                            <span class="badge badge-dot"></span>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                    @else
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar">
                                                    <img src="{{ $adminNotification->image }}"
                                                         alt=""
                                                         class="w-px-40 h-auto rounded-circle">
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1" title="{{ $adminNotification->title }}">{{ text_shorting($adminNotification->title, 30) }}
                                                    ️</h6>
                                                <small
                                                    class="text-muted">{{ $adminNotification->created_at->diffforhumans() }}</small>
                                            </div>
                                            @if (!$adminNotification->status)
                                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                                    <span
                                                       class="dropdown-notifications-read"><span
                                                            class="badge badge-dot"></span></span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </li>
                            @empty
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <small class="text-muted mb-0">{{ lang('No notifications found') }}</small>
                                </li>
                            @endforelse
                        </ul>
                    </li>
                    <li class="dropdown-menu-footer border-top p-3">
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-primary w-100">{{ lang('View all notifications') }}</a>
                    </li>
                </ul>
            </li>
            <!--/ Notification -->
            <!-- Language -->
            <li class="nav-item">
                <div class="dropdown d-inline">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle text-uppercase" type="button"
                            id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-globe me-2"></i>{{ get_lang() }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                        @foreach ($adminLanguages as $adminLanguage)
                            <li><a class="dropdown-item @if ($adminLanguage->code == get_lang()) active @endif"
                                   href="{{ lang_url($adminLanguage->code) }}">{{ $adminLanguage->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>
            <!-- /Language -->
        </ul>
    </div>

</nav>
