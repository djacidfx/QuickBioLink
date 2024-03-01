<div class="col-lg-4">
    <div class="blog-sidebar ms-lg-auto">
        <form action="{{ route('blog.index') }}" method="GET">
            <div class="search-input position-relative mb-30">
                <i class="fa-regular fa-search -left"></i>
                <input type="text" name="search" class="form-control rounded-3 font-14 shadow-none border -focus-dark" placeholder="{{ lang('Search…', 'blog') }}" value="{{ request('search') ?? '' }}" required>
            </div>
        </form>
        <div class="sidebar-widget mb-30">
            <h4 class="widget-title">{{ lang('Categories', 'blog') }}</h4>
            <div class="category-list mt-30">
                @forelse ($blogCategories as $blogCategory)
                    <a href="{{ route('blog.category', $blogCategory->slug) }}" class="d-flex align-items-center justify-content-between">{{ $blogCategory->name }}</a>
                @empty
                    <span class="text-muted">{{ lang('No categories found', 'blog') }}</span>
                @endforelse
            </div>
        </div>
        <div class="sidebar-widget mb-30">
            <h4 class="widget-title">{{ lang('Recent Posts', 'blog') }}</h4>
            @forelse ($popularBlogArticles as $popularBlogArticle)
                <div class="d-flex align-items-center mt-30 mb-20">
                    <div class="flex-shrink-0 overflow-hidden rounded-2">
                        <img width="70" height="70" src="{{ asset('storage/blog/articles/'.$popularBlogArticle->image) }}" alt="{{ $popularBlogArticle->title }}">
                    </div>
                    <div class="flex-shrink-1 ms-3">
                        <h6 class="fw-semibold mb-0"><a href="{{ route('blog.article', $popularBlogArticle->slug) }}">{{ $popularBlogArticle->title }}</a></h6>
                        <p>{{ date_formating($popularBlogArticle->created_at) }}</p>
                    </div>
                </div>
            @empty
                <span class="text-muted text-center">{{ lang('No articles found', 'blog') }}</span>
            @endforelse
        </div>
        @if(!empty($blogTags))
        <div class="sidebar-widget mb-30 pb-20">
            <h4 class="widget-title">{{ lang('Tags', 'blog') }}</h4>
            <div class="tag-list mt-30">
                @foreach($blogTags as $tag)
                    @if(!empty(trim($tag)))
                        <a href="{{ route('blog.tag', trim($tag)) }}">{{ trim($tag) }}</a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

