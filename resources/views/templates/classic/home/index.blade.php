@extends($activeTheme.'layouts.main')
@section('navclass', 'nav-dark')
@section('title', $SeoConfiguration->title ?? '')
@section('content')

    <section class="hero-wrapper -style2">
        <div class="bg-dark-1 overflow-hidden">
            <div class="pt-180 pb-180 lg-pb-120 text-center position-relative">
                <img src="{{ asset($activeThemeAssets.'assets/images/shape/spider-web-hero-bg.webp') }}" class="hero-bg-overlay-image position-absolute">
                <div class="row position-relative mb-100 md-mb-0 lg-mb-30 px-20">
                    <div class="col-lg-8 col-xxl-7 mx-auto position-relative">
                        <div>
                            <h1 class="display-1 sm-font-50 md-font-60 mb-24 fw-bold text-white">
                                {{ lang('Biolinks in Seconds')  }}
                            </h1>
                            <p class="lead font-18 mb-24 text-light-3">{{ lang("Make everything you promote on social searchable to help your followers find exactly what they're looking for. It’s easier than you think.")  }}</p>
                        </div>
                        <div class="d-flex justify-content-center " data-cues="slideInDown" data-delay="600">
                            <div class="position-relative">
                                <a href="{{ route('register') }}" class="button -primary h-48-px transform-none"><i class="fa-solid fa-stars mr-5"></i> {{ lang('Get Started')  }}
                                </a>
                            </div>
                        </div>
                        <!-- /div -->
                    </div>
                    <!-- /column -->
                </div>
                <!-- /.row -->
            </div>
        </div>
        <!-- /.container -->
    </section>
    <!-- /section Hero Cover Image-->
    <section class="d-none d-lg-block">
        <div class="container">
            <div class="hero-cover-image position-relative z-in-9">
                <div class="d-flex align-items-center gap-3">
                    <div class="w-25 d-flex flex-column">
                        <img src="{{ asset($activeThemeAssets.'assets/images/screen/bio-social1.png') }}" class="w-100 h-auto rounded-3 shadow-2 mb-16">
                        <img src="{{ asset($activeThemeAssets.'assets/images/screen/bio-social.png') }}" class="w-100 h-auto rounded-3 shadow-2">
                    </div>
                    <div class="w-25">
                        <img src="{{ asset($activeThemeAssets.'assets/images/screen/bio-theme1.png') }}" class="w-100 h-auto rounded-3 shadow-2">
                    </div>
                    <div class="w-25">
                        <img src="{{ asset($activeThemeAssets.'assets/images/screen/bio-theme2.png') }}" class="w-100 h-auto rounded-3 shadow-2">
                    </div>

                    <div class="w-25 d-flex flex-column">
                        <img src="{{ asset($activeThemeAssets.'assets/images/screen/bio-profile.png') }}" class="w-100 h-auto rounded-3 shadow-2 mb-16">
                        <img src="{{ asset($activeThemeAssets.'assets/images/screen/bio-social2.png') }}" class="w-100 h-auto rounded-3 shadow-2">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /section  Hero Cover Image-->
    {!! ads_on_home_top() !!}
    <!-- / Section CTA Type 1-->
    <section class="pt-80 pb-120 lg-pb-80">
        <div class="cta-banner mx-auto  position-relative">
            <div class="container">
                <div class="row g-5 align-items-start align-items-xl-center">
                    <div class="col-lg-7 col-xl-6 d-none d-lg-block">
                        <div class="position-relative mb-35 sm-mb-0 wow fadeInRight" data-wow-delay="300ms">
                            <div class="cta-widget item-box">
                                <h5 class="title mb-20"><span class="text-primary fw-semibold underbrush">{{ lang('Embed')  }}</span>
                                    {{ lang('Your Favorite Apps')  }}</h5>
                                <div class="d-flex  justify-content-between align-items-center bg-white py-2 px-3 border text-border-1 rounded-3 mb-16">
                                    <div class="d-flex align-items-center">
                                        <span class="pr-20 text-facebook"><i class="fa-brands fa-facebook"></i></span>
                                        <div class="text-capitalize font-16">{{ lang('Facebook')  }}</div>
                                    </div>
                                    <div class="drag-handle cursor-grab">
                                        <i class="fa-solid fa-grip-dots-vertical font-30 text-light-3"></i>
                                    </div>
                                </div>
                                <div class="d-flex  justify-content-between align-items-center bg-white py-2 px-3 border text-border-1 rounded-3 mb-16">
                                    <div class="d-flex align-items-center">
                                        <span class="pr-20 text-instagram"><i class="fa-brands fa-instagram"></i></span>
                                        <div class="text-capitalize font-16">{{ lang('Instagram')  }}</div>
                                    </div>
                                    <div class="drag-handle cursor-grab">
                                        <i class="fa-solid fa-grip-dots-vertical font-30 text-light-3"></i>
                                    </div>
                                </div>
                                <div class="d-flex  justify-content-between align-items-center bg-white py-2 px-3 border text-border-1 rounded-3 mb-16">
                                    <div class="d-flex align-items-center">
                                        <span class="pr-20 text-youtube"><i class="fa-brands fa-youtube"></i></span>
                                        <div class="text-capitalize font-16">{{ lang('Youtube')  }}</div>
                                    </div>
                                    <div class="drag-handle cursor-grab">
                                        <i class="fa-solid fa-grip-dots-vertical font-30 text-light-3"></i>
                                    </div>
                                </div>
                                <div class="d-flex  justify-content-between align-items-center bg-white py-2 px-3 border text-border-1 rounded-3">
                                    <div class="d-flex align-items-center">
                                        <span class="pr-20 text-twitter"><i class="fa-brands fa-twitter"></i></span>
                                        <div class="text-capitalize font-16">{{ lang('Twitter')  }}</div>
                                    </div>
                                    <div class="drag-handle cursor-grab">
                                        <i class="fa-solid fa-grip-dots-vertical font-30 text-light-3"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="cta-widget item-box -style2 d-none d-lg-block">
                                <h5 class="title mb-20 text-center">{{ lang('Manage All Links in')  }} <span class="text-primary fw-semibold underbrush">{{ lang('One Place')  }}</span></h5>
                                <div class="d-flex align-items-center  mb-16">
                                    <img src="{{ asset($activeThemeAssets.'assets/images/biolink-img/1.jpg') }}" alt="Image" class="size-50">
                                    <div class="ml-15">
                                        <div class="text-dark-1 font-14 fw-bold text-one-line word-break">
                                            {{ lang('Summer 2023')  }}
                                        </div>
                                        <div class="text font-14 text-one-line word-break">
                                            {{ lang('www.summerweb.com')  }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center  mb-16">
                                    <img src="{{ asset($activeThemeAssets.'assets/images/biolink-img/2.jpeg') }}" alt="Image" class="size-50">
                                    <div class="ml-15">
                                        <div class="text-dark-1 font-14 fw-bold text-one-line word-break">
                                            {{ lang('Spotify List')  }}
                                        </div>
                                        <div class="text font-14 text-one-line word-break">
                                            {{ lang('www.spotify.com/mylist')  }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center  mb-16">
                                    <img src="{{ asset($activeThemeAssets.'assets/images/biolink-img/3.jpeg') }}" alt="Image" class="size-50">
                                    <div class="ml-15">
                                        <div class="text-dark-1 font-14 fw-bold text-one-line word-break">
                                            {{ lang('Follow me on Insta')  }}
                                        </div>
                                        <div class="text font-14 text-one-line word-break">
                                            {{ lang('https://instagram.com/myinsta')  }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center ">
                                    <img src="{{ asset($activeThemeAssets.'assets/images/biolink-img/4.jpeg') }}" alt="Image" class="size-50">
                                    <div class="ml-15">
                                        <div class="text-dark-1 font-14 fw-bold text-one-line word-break">
                                            {{ lang('Fund Raise')  }}
                                        </div>
                                        <div class="text font-14 text-one-line word-break">
                                            {{ lang('www.donation.com')  }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <img class="d-block d-lg-none w-100" src="assets/images/" alt="">
                            <div class="imgbox position-relative d-none d-xl-block">
                                <img class="img-1 spin-right" src="{{ asset($activeThemeAssets.'assets/images/cta/element-1.png') }}" alt="">
                                <img class="img-2 bounce-x" src="{{ asset($activeThemeAssets.'assets/images/cta/element-2.png') }}" alt="">
                                <img class="img-3 bounce-y" src="{{ asset($activeThemeAssets.'assets/images/cta/element-3.png') }}" alt="">
                                <img class="img-4 bounce-y" src="{{ asset($activeThemeAssets.'assets/images/cta/element-4.png') }}" alt="">
                                <img class="img-5 spin-right" src="{{ asset($activeThemeAssets.'assets/images/cta/element-5.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-5 col-xl-5 offset-xl-1">
                        <div class="mt-100 lg-mt-0 wow fadeInLeft" data-wow-delay="300ms">
                            <div class="badge bg-primary-l text-primary text-uppercase rounded-pill px-3 fw-bold font-12 py-2 mb-3">
                                {{ lang('Features')  }}
                            </div>
                            <h2 class="title mb10">{{ lang('Connect to Everything')  }} <br>{{ lang('You Love')  }}</h2>
                            <p class="text-dark-3 mb-25 md-mb-30"> {{ lang('Create social bio links for Instagram, YouTube, Twitter, Snapchat, Tiktok, Dribble and more.')  }}</p>
                            <div class="list-style3">
                                <ul>
                                    <li class="mb-20"><span
                                            class="size-20 bg-primary-l rounded-circle mr-5 d-inline-flex align-items-center justify-content-center">
                                            <i class="far fa-check text-primary font-12"></i></span> {{ lang('Easy to manage your links.')  }}
                                    </li>

                                    <li class="mb-20"><span
                                            class="size-20 bg-primary-l rounded-circle mr-5 d-inline-flex align-items-center justify-content-center">
                                            <i class="far fa-check text-primary font-12"></i></span> {{ lang('Create multiple bio links.')  }}
                                    </li>
                                    <li class="mb-20"><span
                                            class="size-20 bg-primary-l rounded-circle mr-5 d-inline-flex align-items-center justify-content-center">
                                            <i class="far fa-check text-primary font-12"></i></span> {{ lang('Share your link anywhere anytime.')  }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- / Section CTA Type 1-->
    <!-- /section Use Cases-->
    <section class="our-cases md-mb-80 mb-100">
        <div class="container theme-gradient-8 rounded-4 wow fadeInUp">
            <div class="p-5 py-60">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title text-center">
                            <div class="badge bg-primary-l text-primary text-uppercase rounded-pill px-3 fw-bold font-12 py-2 mb-3">
                                {{ lang('Use Cases')  }}
                            </div>
                            <h2>{{ lang('Unmatchable features.')  }}</h2>
                            <p class="text">{{ lang('Increase engagement while collecting leads with built-in forms.')  }}</p>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-sm-12 col-lg-4">
                        <div class="card h-100">
                            <div class="bg-primary-l size-60 rounded-4 font-30 d-flex align-items-center justify-content-center">
                                <i class="fa-regular fa-screwdriver-wrench"></i>
                            </div>
                            <div class="details pt-16">
                                <h4 class="title mt-16 mb-16">{{ lang('Create Bio Links')  }}</h4>
                                <p class="text">{{ lang('Easily create & manage all your links in one place: personal website, store, recent video or social post.')  }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-4">
                        <div class="card h-100">
                            <div class="bg-primary-l size-60 rounded-4 font-30 d-flex align-items-center justify-content-center">
                                <i class="fa-regular fa-user-pen"></i>
                            </div>
                            <div class="details pt-16">
                                <h4 class="title mt-16 mb-16">{{ lang('Share')  }}</h4>
                                <p class="text">{{ lang('Share your link on any social or digital platform: Instagram, YouTube, Facebook or TikTok, in messengers or via SMS.')  }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-4">
                        <div class="card h-100">
                            <div class="bg-primary-l size-60 rounded-4 font-30 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-rectangle-vertical-history"></i>
                            </div>
                            <div class="details pt-16">
                                <h4 class="title mt-16 mb-16">{{ lang('Multiple Layouts')  }}</h4>
                                <p class="text">{{ lang('Pick a theme or design your own to make sure your content pops. Your bio link does not have to be boring anymore.')  }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-4">
                        <div class="card h-100">
                            <div class="bg-primary-l size-60 rounded-4 font-30 d-flex align-items-center justify-content-center">
                                <i class="fa-regular fa-palette"></i>
                            </div>
                            <div class="details pt-16">
                                <h4 class="title mt-16 mb-16">{{ lang('Elegant And Perfect')  }}</h4>
                                <p class="text">{{ lang('With a cutting-edge interface, followers clicking on your Url will experience a great visual.')  }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-4">
                        <div class="card h-100">
                            <div class="bg-primary-l size-60 rounded-4 font-30 d-flex align-items-center justify-content-center">
                                <i class="fa-brands fa-edge"></i>
                            </div>
                            <div class="details pt-16">
                                <h4 class="title mt-16 mb-16">{{ lang('Web Based')  }}</h4>
                                <p class="text">{{ lang('No need to install anything, just access anytime via browser from any device.')  }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-4">
                        <div class="card h-100">
                            <div class="bg-primary-l size-60 rounded-4 font-30 d-flex align-items-center justify-content-center">
                                <i class="fa-regular fa-laptop-mobile"></i>
                            </div>
                            <div class="details pt-16">
                                <h4 class="title mt-16 mb-16">{{ lang('Fully Responsive')  }}</h4>
                                <p class="text">{{ lang('Yes, Quicklink gives you the biggest selection of visual layouts and all layouts are fully responsive so they look great on all devices.')  }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-4">
                        <div class="card h-100">
                            <div class="bg-primary-l size-60 rounded-4 font-30 d-flex align-items-center justify-content-center">
                                <i class="fa-regular fa-headset"></i>
                            </div>
                            <div class="details pt-16">
                                <h4 class="title mt-16 mb-16">{{ lang('SEO Friendly')  }}</h4>
                                <p class="text">{{ lang('Bring more organic traffic to your website with this SEO friendly feature.')  }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-4">
                        <div class="card h-100">
                            <div class="bg-primary-l size-60 rounded-4 font-30 d-flex align-items-center justify-content-center">
                                <i class="fa-regular fa-icons"></i>
                            </div>
                            <div class="details pt-16">
                                <h4 class="title mt-16 mb-16">{{ lang('Font Awesome 6 Pro Icons')  }}</h4>
                                <p class="text">{{ lang('All the icons are font based and ready to match the quality of any HQ screen.')  }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-4">
                        <div class="card h-100">
                            <div class="bg-primary-l size-60 rounded-4 font-30 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div class="details pt-16">
                                <h4 class="title mt-16 mb-16">{{ lang('Lifetime Update')  }}</h4>
                                <p class="text">{{ lang('We keep updating our products to stay up to date with latest trends and technology.')  }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /section  Use Cases-->
    <!-- / Section CTA Type 2-->
    <div class="cta-banner -type3 mx-auto md-mb-80 mb-100 position-relative overflow-hidden">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 wow fadeInRight">
                    <div class="main-title mb-30">
                        <div class="badge bg-primary-l text-primary text-uppercase rounded-pill px-3 fw-bold font-12 py-2 mb-3">
                            {{ lang('Update Anytime')  }}
                        </div>
                        <h2 class="title">{{ lang('Make your links work for you')  }}</h2>
                        <p></p>
                    </div>
                    <div class="position-relative">
                        <div class="d-flex align-items-start mb-30">
                            <span class="flex-shrink-0 font-30 text-dark-3"><i
                                    class="fa-regular fa-badge-check"></i></span>
                            <div class="flex-grow-1 ml-20">
                                <h4 class="mb-1">{{ lang('Connect social')  }}</h4>
                                <p class="text-dark-3 mb-0 font-14">{{ lang('Seamlessly connect your Quick bio link with the tools you')  }}<br class="d-none d-lg-block"> {{ lang('already use.')  }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-30">
                            <span class="flex-shrink-0 font-30 text-dark-3"><i
                                    class="fa-regular fa-badge-check"></i></span>
                            <div class="flex-grow-1 ml-20">
                                <h4 class="mb-1">{{ lang('Choose your own')  }}</h4>
                                <p class="text-dark-3 mb-0 font-14">{{ lang('Customize your Quick bio link to match your brand. Make it')  }}<br class="d-none d-lg-block"> {{ lang('feel like you.')  }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-30">
                            <span class="flex-shrink-0 font-30 text-dark-3"><i
                                    class="fa-regular fa-badge-check"></i></span>
                            <div class="flex-grow-1 ml-20">
                                <h4 class="mb-1">{{ lang('Easy to manage')  }}</h4>
                                <p class="text-dark-3 mb-0 font-14">{{ lang('Add, Manage, and update content with our quick, easy editor.')  }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block offset-1">
                    <img class="wow fadeInLeft" src="{{ asset($activeThemeAssets.'assets/images/cta/cta2.png') }}" alt="call to action image" data-wow-delay="300ms">
                </div>
            </div>
        </div>
    </div>
    <!-- / Section CTA Type 2-->

    {{--Testimonials Section--}}
    @if ($testimonials->count() > 0)
        <section class="our-testimonials bg-danger-l md-mb-80 mb-100">
            <div class="container pt-100 pb-80 md-py-80 wow fadeInUp">
                <div class="row gx-xl-5 gy-5">
                    <div class="col-xl-4">
                        <div class="badge bg-primary-l text-primary text-uppercase rounded-pill px-3 fw-bold font-12 py-2 mb-3">
                            {{ lang('Testimonials')  }}
                        </div>
                        <h2 class="mt-10 mb-3">{{ lang('Our Community') }}</h2>
                        <p class="mb-6">{{ lang('Customer satisfaction is our major goal. See what our clients are saying about our services.') }}</p>
                        <a href="#" class="button -lg -primary rounded-pill font-18">{{ lang('All Testimonials') }}</a>
                    </div>
                    <!-- /column -->
                    <div class="col-xl-8">
                        <div class="reviews-slider3 owl-carousel owl-theme">
                            @foreach ($testimonials as $testimonial)
                                <div class="card">
                                    <div class="card-body">
                                        <blockquote class="icon mb-0">
                                            <p>“{{ !empty($testimonial->translations->{current_language()->code}->content)
                                        ? $testimonial->translations->{current_language()->code}->content
                                        : $testimonial->content }}”</p>
                                            <div class="blockquote-details d-flex align-items-center">
                                                <div class="w-50-px">
                                                    <img class="rounded-circle" src="{{ asset('storage/testimonials/'.$testimonial->image) }}" alt="{{ $testimonial->name }}" />
                                                </div>

                                                <div class="info ml-8">
                                                    <h5 class="mb-1">{{$testimonial->name}}</h5>
                                                    <p class="mb-0">
                                                        {{ !empty($testimonial->translations->{current_language()->code}->designation)
                                                        ? $testimonial->translations->{current_language()->code}->designation
                                                        : $testimonial->designation }}
                                                    </p>
                                                </div>
                                            </div>
                                        </blockquote>
                                    </div>
                                    <!--/.card-body -->
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{--Blog Section--}}
    @if ($settings->blog->status && $settings->blog->show_on_home && $blogArticles->count() > 0)
        <!--START -- Blog Grid style - on hover border dark and no shadow -- START-->
        <section class="our-blog md-mb-80 mb-100">
            <div class="container">
                <div class="row wow fadeInUp align-items-center">
                    <div class="col-lg-9">
                        <div class="main-title">
                            <div class="badge bg-primary-l text-primary text-uppercase rounded-pill px-3 fw-bold font-12 py-2 mb-3">
                                {{ lang('Blogs')  }}
                            </div>
                            <h2 class="title">{{ lang('Our Blog') }}</h2>
                            <p class="paragraph">{{ lang('blog section description') }}</p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="text-start text-lg-end mb-4">
                            <a class="button push-right" href="{{ route('blog.index') }}">{{ lang('Browse All') }}<i class="fal fa-arrow-right-long ml-5 push-this"></i></a>
                        </div>
                    </div>
                </div>
                <div class="row wow fadeInUp" data-wow-delay="300ms">
                    @foreach ($blogArticles as $blogArticle)
                        <div class="col-sm-6 col-xl-3">
                            <div class="blog-wrap border border-1 -hover-dark shadow-none">
                                <div class="blog-img"><img  class="w-100" src="{{ asset('storage/blog/articles/'.$blogArticle->image) }}" alt="{{ $blogArticle->title }}"></div>
                                <div class="blog-content">
                                    <a class="date" href="{{ route('blog.article', $blogArticle->slug) }}">{{ date_formating($blogArticle->created_at) }}</a>
                                    <h4 class="title mt-1"><a href="{{ route('blog.article', $blogArticle->slug) }}" class="text-decoration -underline">{{ $blogArticle->title }}</a></h4>
                                    <p class="text mb-0">{{ $blogArticle->short_description }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <!--END -- Blog Grid style - on hover border dark and no shadow -- END-->
    @endif

    {{--FAQS Section--}}
    @if (@$settings->enable_faqs && $faqs->count() > 0)
        <section class="our-faq md-mb-80 mb-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 m-auto wow fadeInUp" data-wow-delay="300ms">
                        <div class="main-title text-center">
                            <div class="badge bg-primary-l text-primary text-uppercase rounded-pill px-3 fw-bold font-12 py-2 mb-3">
                                {{ lang('FAQs')  }}
                            </div>
                            <h2 class="title">{{ lang('Frequently Asked Questions') }}</h2>
                            <p class="paragraph mt10">{{ lang('faqs description') }}</p>
                        </div>
                    </div>
                </div>
                <div class="row wow fadeInUp" data-wow-delay="300ms">
                    <div class="col-lg-8 mx-auto">
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
                                            <div id="collapse{{ hashid($faq->id) }}" class="accordion-collapse collapse @if($key == 0) show @endif" aria-labelledby="heading{{ hashid($faq->id) }}" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">{!! $faq->content !!}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="our-cta md-mb-80 mb-100">
        <div class="container theme-gradient-3 rounded-4">
            <div class="p-5 py-100 text-center">
                <div class="row text-center">
                    <div class="col-xl-11 col-xxl-9 mx-auto">
                        <h2 class="text-uppercase font-16 mb-16">{{ lang('Join Our Community') }}</h2>
                        <h3 class="display-6 px-32 pb-32">{{ lang('We are trusted by over 5000+ clients. Join them now and grow your business.') }}</h3>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <span><a href="{{ route('register') }}" class="button bg-dark-1 text-white">{{ lang('Get Started') }}</a></span>
                </div>
            </div>
        </div>
    </section>

    {!! ads_on_home_bottom() !!}
@endsection
