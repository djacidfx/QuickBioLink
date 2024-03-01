<h5 class="mb-0">{{ admin_lang('Pre-build Pages') }}</h5>
<hr>

<div class="mb-1"><strong>{{ admin_lang('Pricing') }}</strong> : <a href="{{ route('pricing') }}"
                                                                    target="_blank">{{ route('pricing') }}</a>
</div>
@if (@$settings->blog->status)
    <div class="mb-1"><strong>{{ admin_lang('Blog') }}</strong> : <a href="{{ route('blog.index') }}"
                                                                     target="_blank">{{ route('blog.index') }}</a>
    </div>
@endif
@if (@$settings->enable_faqs)
    <div class="mb-1"><strong>{{ admin_lang('FAQs') }}</strong> : <a href="{{ route('faqs') }}"
                                                                     target="_blank">{{ route('faqs') }}</a>
    </div>
@endif
@if (@$settings->enable_contact_page)
    <div><strong>{{ admin_lang('Contact') }}</strong> : <a href="{{ route('contact') }}"
                                                           target="_blank">{{ route('contact') }}</a>
    </div>
@endif