@extends($activeTheme.'layouts.app')
@section('title', lang('Dashboard', 'account'))
@section('content')
    <div class="d-flex justify-content-between align-items-center pb-30">
        <div class="title-head">
            <h1 class="mb-0">{{ lang('Dashboard', 'account') }}</h1>
        </div>
    </div>
    <!-- / Dashboard Tiles-->
    <div class="card mb-50">
        <div class="d-state -style2 d-md-flex bg-light-1 rounded-3 justify-content-md-around">
            <!-- / Tiles Box-->
            <div class="d-state-box text-center fw-bold py-24">
                <div class="badge bg-info text-white mb-16">{{ lang('My Bio Links', 'dashboard') }}</div>
                <div class="text-dark-1 font-30 md-font-24 sm-font-20 lh-1 mb-5">{{ count($posts) }}</div>
                <p class="d-flex align-items-center justify-content-center"><i class="fa-regular fa-contact-card mr-5"></i> {{ lang('Total Bio Links', 'dashboard') }}</p>
            </div>
            <!-- / Tiles Box-->
            <!-- / Tiles Box Separator -->
            <span class="d-state-separator separator-right-2px-op-l d-none d-md-block"></span>
            <!-- / Tiles Box Separator -->
            <!-- / Tiles Box-->
            <div class="d-state-box rounded-3 text-center fw-bold py-24">
                <div class="badge bg-danger text-white mb-16">{{ lang('Membership', 'dashboard') }}</div>
                @if ($user->isSubscribed() && !$user->subscription->isCancelled())
                    <div class="text-dark-1 font-30 md-font-24 sm-font-20 lh-1 mb-5">{{ $user->subscription->plan->name }}</div>
                    <p class="d-flex align-items-center justify-content-center"><i class="fa-regular fa-clock mr-5"></i> {{ format_interval($user->subscription->plan->interval) }}</p>
                @else
                    <div class="text-dark-1 font-30 md-font-24 sm-font-20 lh-1 mb-5">--</div>
                    <p class="d-flex align-items-center justify-content-center"><i class="fa-regular fa-clock mr-5"></i> Not Subscribe</p>
                @endif

            </div>
            <!-- / Tiles Box-->
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center pb-30">
        <div class="title-head">
            <h1 class="mb-0">{{ lang('My Bio Pages', 'account') }}</h1>
        </div>
        <a href="{{ route('biolinks.create') }}" class="button -secondary ml-auto rounded-pill">{{ lang('Add New', 'account') }} <i class="fa-regular fa-plus ml-5"></i></a>
    </div>
    <section class="products-list">
        @if ($posts->count() > 0)
            @foreach ($posts as $post)
                <div class="card px-20 py-20  wow fadeInUp">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('storage/post/logo/'.$post->image) }}" alt="{{ $post->title }}" class="size-60 sm-w-40-px sm-h-40-px object-fit-cover">
                            <div class="ml-24">
                                <h5 class="mb-0"><a href="{{ url('/') }}/{{ $post->slug }}" target="_blank">{{ $post->title }}</a></h5>
                                <p class="d-none d-sm-block">{{ $post->content }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-4">
                            <div class="d-flex align-items-center">
                                <i class="fa-regular fa-eye text-grey-1"></i>
                                <span class="count ml-5">{{ format_number_count($post->views) }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('biolinks.edit', $post->id) }}" class="icon-group -outlined -info rounded-2 sm-h-30-px sm-w-30-px size-32"><i class="fa-regular fa-edit font-16"></i></a>
                                <form class="d-inline"
                                      action="{{ route('biolinks.destroy', $post->id) }}"
                                      method="POST" onsubmit='return confirm("{{ lang('Are you sure?') }}")'>
                                    @method('DELETE')
                                    @csrf
                                    <button class="icon-group -outlined -danger rounded-2 sm-h-30-px sm-w-30-px size-32" type="submit"><i class="fa-regular fa-xmark font-16"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            @include($activeTheme.'user.empty-section')
        @endif
    </section>
@endsection
