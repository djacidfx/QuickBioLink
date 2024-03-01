<?php
use App\Models\Advertisement;

/**
 * @param $key
 * @return null
 */
function ads($key)
{
    $ad = Advertisement::where([['key', $key], ['status', 1]])->first();
    if (subscription() && subscription()->is_subscribed) {
        if (subscription()->plan->advertisements) {
            return $ad;
        } else {
            return null;
        }
    }
    return $ad;
}

/**
 * Head code for ads
 */
function head_code()
{
    if (ads('head_code')) {
        return ads('head_code')->code;
    }
}

/**
 * Home page top ad
 *
 * @return string|void
 */
function ads_on_home_top()
{
    if (ads('home_page_top')) {
        return '<center>
           <div class="google-ads-728x90 mb-40">' . ads('home_page_top')->code . '</div>
        </center>';
    }
}

/**
 * Home page bottom ad
 *
 * @return string|void
 */
function ads_on_home_bottom()
{
    if (ads('home_page_bottom')) {
        return '<center>
           <div class="google-ads-728x90 mb-40">' . ads('home_page_bottom')->code . '</div>
        </center>';
    }
}

/**
 * Blog page top ad
 *
 * @return string|void
 */
function ads_on_blog_top()
{
    if (ads('blog_page_top')) {
        return '<center>
           <div class="google-ads-728x90 mb-40">' . ads('blog_page_top')->code . '</div>
        </center>';
    }
}

/**
 * Blog page bottom ad
 *
 * @return string|void
 */
function ads_on_blog_bottom()
{
    if (ads('blog_page_bottom')) {
        return '<center>
           <div class="google-ads-728x90 mb-40">' . ads('blog_page_bottom')->code . '</div>
        </center>';
    }
}
