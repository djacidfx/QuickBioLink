@extends('admin.layouts.main')
@section('title', admin_lang('Notifications (' . $unreadNotificationsCount . ')'))
@section('header_buttons')
    <a class="btn btn-outline-primary ms-2" href="{{ route('admin.notifications.readall') }}">{{ admin_lang('Mark All as Read') }}</a>
    <form class="d-inline ms-2" action="{{ route('admin.notifications.deleteallread') }}"
          method="POST" onsubmit='return confirm("{{admin_lang('Are you sure?')}}")'>
        @csrf
        @method('DELETE')
        <button class="btn btn-icon btn-danger" title="{{ admin_lang('Delete All Read') }}" data-tippy-placement="top">
            <i class="icon-feather-trash-2"></i>
        </button>
    </form>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="dataTables_wrapper">
                <table id="basic_datatable" class="table table-striped">
                    <thead>
                    <tr>
                        <th class="w-px-50">{{ admin_lang('Type') }}</th>
                        <th>{{ admin_lang('Title') }}</th>
                        <th>{{ admin_lang('Created') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($notifications as $notification)
                        <tr class="item">
                            <td>
                                @if ($notification->link)
                                    <a href="{{ route('admin.notifications.view', hashid($notification->id)) }}">
                                        @endif
                                        <div class="avatar">
                                            @if ($notification->type == 'new_user')
                                                <span class="avatar-initial rounded bg-label-success"><i
                                                        class="fas fa-user-plus"></i></span>
                                            @elseif ($notification->type == 'new_comment')
                                                <span class="avatar-initial rounded bg-label-warning"><i
                                                        class="fas fa-comment"></i></span>
                                            @endif
                                        </div>
                                        @if ($notification->link)
                                    </a>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if ($notification->link)
                                        <a class="text-body"
                                           href="{{ route('admin.notifications.view', hashid($notification->id)) }}">
                                            @endif
                                            {{ $notification->title }}
                                            @if ($notification->link)
                                        </a>
                                    @endif
                                    @if (!$notification->status)
                                        <span class="badge badge-dot bg-primary ms-1"></span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $notification->created_at->diffforhumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                @include('admin.includes.empty')
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
