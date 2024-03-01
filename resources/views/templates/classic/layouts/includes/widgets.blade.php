{!! google_analytics() !!}
{!! tawk_to() !!}

@if(@$settings->enable_cookie_consent_box && !isset($_COOKIE['quick_cookie_accepted']))
<div class="cookieConsentContainer">
    <div class="cookieTitle">
        <h3>{{ lang('Cookies') }}</h3>
    </div>
    <div class="cookieDesc">
        <p>{{ lang('gdpr_cookie_note') }}
            @if(!empty($settings->cookie_policy_link))
            <a class="text-primary" href="{{$settings->cookie_policy_link}}">{{ lang('Cookie Policy') }}</a>
            @endif
        </p>
    </div>
    <div class="cookieButton">
        <a href="javascript:void(0)" class="button -primary cookieAcceptButton">{{ lang('Accept') }}</a>
    </div>
</div>
@endif
