@extends($activeTheme.'layouts.main')
@section('title', lang('FAQs'))
@section('content')
    <section class="page-banner-area theme-gradient-3 pt-170 @if (ads('home_page_top')) mb-40 @else mb-70 @endif">
        <div class="container">
            <div class="row wow fadeInUp" data-wow-delay="300ms">
                <div class="col-md-10 col-xl-8 mx-auto">
                    <div class="d-flex flex-column align-items-center text-center">
                        <h2>{{ lang('FAQs') }}</h2>
                        <p>{{ lang('faqs description') }}</p>
                        <ol class="breadcrumb text-grey-2">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ lang('Home') }}</a></li>
                            <li class="breadcrumb-item active text-dark-1" aria-current="page">{{ lang('FAQs') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {!! ads_on_home_top() !!}
    <section class="our-feature-request pb-100">
        <div class="container">
            <div class="row wow fadeInUp">
                <div class="col-xl-8 offset-xl-2">
                    <div class="ui-content">
                        <div class="accordion -style2 faq-page mb-4 mb-lg-5">
                            <div class="accordion" id="accordionExample">
                                @foreach ($faqs as $key => $faq)
                                    <div class="accordion-item @if($key == 0) active @endif">
                                        <h2 class="accordion-header" id="heading{{ hashid($faq->id) }}">
                                            <button class="accordion-button @if($key != 0) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ hashid($faq->id) }}" aria-expanded="" aria-controls="collapse{{ hashid($faq->id) }}">
                                                {{ $faq->title }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ hashid($faq->id) }}" class="accordion-collapse collapse @if($key == 0) show @endif" aria-labelledby="heading{{ hashid($faq->id) }}" data-parent="#accordionExample">
                                            <div class="accordion-body">{!! $faq->content !!}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        {{ $faqs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
    {!! ads_on_home_bottom() !!}
@endsection
