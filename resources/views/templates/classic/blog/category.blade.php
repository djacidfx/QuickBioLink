@extends($activeTheme.'layouts.main')
@section('title', $blogCategory->name)
@section('content')
    <section class="page-banner-area theme-gradient-3 pt-170 @if (ads('blog_page_top')) mb-40 @else mb-70 @endif">
        <div class="container">
            <div class="row wow fadeInUp" data-wow-delay="300ms">
                <div class="col-md-10 col-xl-8 mx-auto">
                    <div class="d-flex flex-column align-items-center">
                        <h2>{{ $blogCategory->name }}</h2>
                        <ol class="breadcrumb text-grey-2">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ lang('Home', 'pages') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">{{ lang('Blog', 'pages') }}</a></li>
                            <li class="breadcrumb-item active text-dark-1" aria-current="page">{{ $blogCategory->name }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {!! ads_on_blog_top() !!}
    <section class="our-blog pb-120">
        <div class="container">
            <div class="row wow fadeInUp" data-wow-delay="300ms">
                <div class="col-lg-8">
                    @if ($blogArticles->count() > 0)
                        <div class="row">
                            @foreach ($blogArticles as $blogArticle)
                                <div class="col-md-6">
                                    <div class="blog-wrap border border-1 -hover-dark shadow-none">
                                        <div class="blog-img"><img class="w-100" src="{{ asset('storage/blog/articles/'.$blogArticle->image) }}" alt="{{ $blogArticle->title }}"></div>
                                        <div class="blog-content">
                                            <a class="date" href="{{ route('blog.article', $blogArticle->slug) }}">{{ date_formating($blogArticle->created_at) }}</a>
                                            <h4 class="title mt-1"><a href="{{ route('blog.article', $blogArticle->slug) }}" class="text-decoration -underline">{{ $blogArticle->title }}</a></h4>
                                            <p class="text mb-0">{{ $blogArticle->short_description }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="md-mb-40">
                                {{ $blogArticles->links() }}
                            </div>
                        </div>
                    @else
                        <span class="text-muted">{{ lang('No articles found', 'blog') }}</span>
                    @endif
                </div>
                @include($activeTheme.'blog.sidebar')
            </div>
        </div>
    </section>
    {!! ads_on_blog_bottom() !!}
@endsection