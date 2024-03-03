<?php
use App\Http\Methods\SubscriptionManager;
use App\Models\AdminNotification;
use App\Models\Country;
use App\Models\Language;
use App\Models\MailTemplate;
use App\Models\PlanOption;
use App\Models\PostOption;
use App\Models\Settings;
use App\Models\Tax;
use App\Models\User;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;
use Vinkla\Hashids\Facades\Hashids;


/**
 * Check demo mode is enabled or disabled
 *
 * @return bool
 */
function demo_mode()
{
    if (Auth::user() && Auth::user()->id == 1) {
        return false;
    }
    if (env('DEMO_MODE')) {
        return true;
    }
    return false;
}

/**
 * Get logged user details
 *
 * @return null
 */
function user_auth_info()
{
    $info = null;
    if (Auth::user()) {
        $info = User::where('id', Auth::user()->id)->with('subscription')->first();
    }
    return $info;
}

/**
 * Get user ip address
 *
 * @return mixed|null
 */
function user_ip()
{
    $ip = null;
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
    } else {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
    }
    return $ip;
}

/**
 * Get user location data with ip address
 *
 * @param null $ip
 * @return array
 */
function user_ip_lookup($ip = null)
{
    $ip = ($ip) ? $ip : user_ip();
    if (Cache::has($ip)) {
        $ipInfo = Cache::get($ip);
    } else {
        $fields = "status,country,countryCode,city,zip,lat,lon,timezone,query";
        $ipInfo = (object) json_decode(curl_get_file_contents("http://ip-api.com/json/{$ip}?fields={$fields}"), true);
        Cache::forever($ip, $ipInfo);
    }
    $data['ip'] = $ipInfo->query ?? $ip;
    $data['location']['country'] = $ipInfo->country ?? "Other";
    $data['location']['country_code'] = $ipInfo->countryCode ?? "Other";
    $data['location']['timezone'] = $ipInfo->timezone ?? "Other";
    $data['location']['city'] = $ipInfo->city ?? "Other";
    $data['location']['postal_code'] = $ipInfo->zip ?? "Unknown";
    $data['location']['latitude'] = $ipInfo->lat ?? "Unknown";
    $data['location']['longitude'] = $ipInfo->lon ?? "Unknown";
    return $data;
}


/**
 * Get user location, operating system, web browser details
 *
 * @return mixed
 */
function user_ip_info()
{
    $lookupData = user_ip_lookup();
    $lookupData['system']['os'] = user_os_info();
    $lookupData['system']['browser'] = user_browser_info();
    return array_to_object($lookupData);
}

/**
 * Get user operating system
 *
 * @return string
 */
function user_os_info()
{
    $operating_systems = [
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile',
    ];

    $os = "Other";
    foreach ($operating_systems as $key => $value) {
        if (preg_match($key, $_SERVER['HTTP_USER_AGENT'])) {
            $os = $value;
        }
    }
    return $os;
}

/**
 * Get user web browser details
 *
 * @return string
 */
function user_browser_info()
{
    $browsers = [
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser',
    ];

    $browser = "Other";
    foreach ($browsers as $key => $value) {
        if (preg_match($key, $_SERVER['HTTP_USER_AGENT'])) {
            $browser = $value;
        }
    }
    return $browser;
}


/**
 * @param $URL
 * @return bool|string
 */
function curl_get_file_contents($URL)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);
    if ($contents) {
        return $contents;
    } else {
        return false;
    }
}

/**
 * Update log for the user
 *
 * @param $user
 */
function update_user_logs($user = null){
    $ipInfo = user_ip_info();
    $ip = $ipInfo->ip;

    if($user)
        $loginLog = UserLog::where([['user_id', $user->id], ['ip', $ip]])->first();
    else
        $loginLog = null;

    $location = $ipInfo->location->city . ', ' . $ipInfo->location->country;
    if ($loginLog != null) {
        $loginLog->country = $ipInfo->location->country;
        $loginLog->country_code = $ipInfo->location->country_code;
        $loginLog->timezone = $ipInfo->location->timezone;
        $loginLog->location = $location;
        $loginLog->latitude = $ipInfo->location->latitude;
        $loginLog->longitude = $ipInfo->location->longitude;
        $loginLog->browser = $ipInfo->system->browser;
        $loginLog->os = $ipInfo->system->os;
        $loginLog->update();
    } else {
        $newloginLog = new UserLog();
        $newloginLog->user_id = $user->id;
        $newloginLog->ip = $ipInfo->ip;
        $newloginLog->country = $ipInfo->location->country;
        $newloginLog->country_code = $ipInfo->location->country_code;
        $newloginLog->timezone = $ipInfo->location->timezone;
        $newloginLog->location = $location;
        $newloginLog->latitude = $ipInfo->location->latitude;
        $newloginLog->longitude = $ipInfo->location->longitude;
        $newloginLog->browser = $ipInfo->system->browser;
        $newloginLog->os = $ipInfo->system->os;
        $newloginLog->save();
    }
}


/**
 * @param null $key
 * @return false|mixed
 */
function settings($key = null)
{
    if (!empty($key)) {
        return Settings::selectSettings($key);
    }
    $settings = Settings::pluck('value', 'key')->all();
    return array_to_object($settings);
}


/**
 * Set env
 *
 * @param $key
 * @param $value
 * @param false $quote
 */
function set_env($key, $value, $quote = false)
{
    $path = app()->environmentFilePath();
    $value = ($quote) ? '"' . $value . '"' : $value;

    if(is_bool(env($key))) {
        $old = env($key)? 'true' : 'false';
    }
    elseif(env($key)===null) {
        $old = 'null';
    }
    else {
        $old = ($quote) ? '"' . env($key) . '"' : env($key);
    }

    if (file_exists($path)) {
        $str = file_get_contents($path);
        if(str_contains($str, "$key=" . $old)) {
            file_put_contents($path, str_replace(
                "$key=" . $old, "$key=" . $value, $str
            ));
        } else {
            file_put_contents($path, $str . "\n$key=" . $value);
        }
    }
}

/**
 * Active theme path
 *
 * @param false $asset
 * @return string
 * @throws \Psr\Container\ContainerExceptionInterface
 * @throws \Psr\Container\NotFoundExceptionInterface
 */
function active_theme($asset = false)
{
    $template = env('THEME_NAME');
    $sess = session()->get('template');
    if (trim((string)$sess)) {
        $template = $sess;
    }
    if ($asset) return 'templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

/**
 * Get active theme name
 *
 * @return mixed|object
 * @throws \Psr\Container\ContainerExceptionInterface
 * @throws \Psr\Container\NotFoundExceptionInterface
 */
function active_theme_name()
{
    $template = env('THEME_NAME');
    $sess = session()->get('template');
    if (trim($sess)) {
        $template = $sess;
    }
    return $template;
}

/**
 * Get page title
 *
 * @param $env
 * @return string
 */
function page_title($env)
{
    $name = settings('site_title');
    $title = null;
    if ($env->yieldContent('title')) {
        $title = $env->yieldContent('title') . ' — ';
    }
    return $title . $name;
}

/**
 * Get post options
 *
 * @param $postId
 * @param null $key
 * @return false|mixed
 */
function post_options($postId, $key = null)
{
    if (!empty($key)) {
        return PostOption::getPostOption($postId, $key);
    }
    $options = PostOption::where('post_id',$postId)->pluck('value', 'key');
    return array_to_object($options);
}

/**
 * Get subscription details
 *
 * @param null $user
 * @return mixed
 */
function subscription($user = null)
{
    $user = $user ?? user_auth_info();
    return SubscriptionManager::subscription($user);
}

/**
 * Plan interval => Monthly|Yearly|''
 *
 * @param $interval
 * @return string|null
 */
function format_interval($interval)
{
    if ($interval == 1) {
        return lang('monthly');
    } elseif ($interval == 2) {
        return lang('yearly');
    }
    return '';
}

/**
 * Get Plan Options
 *
 * @param $id
 * @return mixed
 */
function plan_option($id)
{
    return PlanOption::where('id', $id)->get();
}

/**
 * Return subscribe button based on type
 *
 * @param $plan
 * @return string
 */
function subscribe_button($plan)
{
    $button = "";
    $buttonText = lang('Subscribe');
    $buttonClass = $plan->isFeatured() ? '-primary' : 'bg-dark-1';

    if (auth()->user()) {

        $user = user_auth_info();
        $subscription = $user->subscription;

        if ($user->isSubscribed() && $plan->id == $subscription->plan->id && !$subscription->isAboutToExpire()) {
            $button = '<button class="button ' . $buttonClass . ' text-white -lg w-100 mt-16 transform-none disabled">' . lang('Active') . '</button>';
        } else {
            $type = "subscribe";
            if ($user->isSubscribed()) {

                if ($plan->id == $subscription->plan->id && $subscription->isAboutToExpire()) {

                    $buttonText = lang('Renew');
                    $type = "renew";

                } elseif ($plan->id != $subscription->plan->id && $plan->price < $subscription->plan->price &&
                    $plan->interval <= $subscription->plan->interval) {

                    $buttonText = lang('Downgrade');
                    $type = "downgrade";

                } elseif ($plan->id != $subscription->plan->id) {
                    $buttonText = lang('Upgrade');
                    $type = "upgrade";
                }
            }

            $action = route('subscribe', [$plan->id, $type]);
            $token = csrf_token();
            $button = '<form action="' . $action . '" method="POST"><input type="hidden" name="_token" value="' . $token . '">
            <button class="button ' . $buttonClass . ' text-white -lg w-100 mt-16">' . $buttonText . '</button>
            </form>';
        }

    } else {

        $button = '<a href="' . route('login') . '"
        class="button ' . $buttonClass . ' w-100 text-white mt-16">' . $buttonText . '</a>';
    }
    return $button;
}

/**
 * Price decimal format
 *
 * @param $price
 * @return string
 */
function price_format($price)
{
    return number_format((float) $price, 2);
}

/**
 * Price decimal format with currency symbol
 *
 * @param $price
 * @return string
 */
function price_symbol_format($price)
{
    if (settings('currency')->position == 1) {
        return settings('currency')->symbol . price_format($price);
    } else {
        return price_format($price) . settings('currency')->symbol;
    }
}

/**
 * Price decimal format with currency code
 *
 * @param $price
 * @return string
 */
function price_code_format($price)
{
    if (settings('currency')->position == 1) {
        return settings('currency')->code . ' ' . price_format($price);
    } else {
        return price_format($price) . ' ' . settings('currency')->code;
    }
}

/**
 * Gat tax info on basis country name
 *
 * @param $countryName
 * @return int
 */
function country_tax($countryName)
{
    $country = Country::where('name', $countryName)->first();
    if (is_null($country)) {
        $country = Country::where('code', user_ip_info()->location->country_code)->first();
    }
    if (!is_null($country)) {
        $tax = Tax::where('country_id', $country->id)->first();
        if (is_null($tax)) {
            $tax = Tax::whereNull('country_id')->first();
            $tax = (is_null($tax)) ? 0 : $tax->percentage;
        } else {
            $tax = $tax->percentage;
        }
    } else {
        $tax = Tax::whereNull('country_id')->first();
        $tax = (is_null($tax)) ? 0 : $tax->percentage;
    }
    return $tax;
}

/**
 * Get countries list array
 *
 * @return \Illuminate\Database\Eloquent\Collection
 */
function countries()
{
    $countries = Country::all();
    return $countries;
}

/**
 * Get email templates
 *
 * @param $key
 * @return mixed
 */
function email_template($key)
{
    $email_template = MailTemplate::where([['lang', get_lang()], ['key', $key]])->first();
    return $email_template;
}

/**
 * Get admin panel path
 *
 * @return mixed|string
 */
function admin_path()
{
    return env('APP_ADMIN') ?? 'admin';
}

/**
 * Check is this admin path or not
 *
 * @return bool
 */
function is_admin_path()
{
    if (str_contains(request()->path(), admin_path() . '/')) {
        return true;
    }
    return false;
}

/**
 * Admin notifications
 * @param $title
 * @param $type
 * @param null $link
 */
function admin_notify($title, $type, $link = null)
{
    $notify = new AdminNotification();
    $notify->title = $title;
    $notify->type = $type;
    $notify->link = $link;
    $notify->save();
}

/**
 * Delete admin notifications
 *
 * @param $link
 */
function delete_admin_notification($link)
{
    $notifications = AdminNotification::where('link', $link)->get();
    if ($notifications->count() > 0) {
        foreach ($notifications as $notification) {
            $notification->delete();
        }
    }
}

/**
 * @return array|string[][]
 */
function localize_options()
{
    if (env('APP_INSTALLED')) {
        if (settings('include_language_code')) {
            return [
                'prefix' => LaravelLocalization::setLocale(),
                'middleware' => ['localize', 'localizationRedirect', 'localeSessionRedirect', 'UserStatusCheck'],
            ];
        } else {
            return [
                'middleware' => ['quickcms.localize', 'UserStatusCheck'],
            ];
        }
    } else {
        return [
            'middleware' => ['notInstalled'],
        ];
    }
}

/**
 * Lang URL
 * @param $lang
 * @return string
 */
function lang_url($lang)
{
    if (settings('include_language_code')) {
        return LaravelLocalization::getLocalizedURL($lang, null, [], true);
    } else {
        return route('localize', $lang);
    }
}

/**
 * Get language
 * @return mixed
 */
function get_lang()
{
    return App::getLocale();
}

/**
 * Get current language
 *
 * @return mixed
 */
function current_language()
{
    $language = Language::where('code', get_lang())->first();
    return $language;
}

/**
 * Get lang flag
 *
 * @return string
 */
function get_lang_flag()
{
    return asset('storage/flags/'.current_language()->flag);
}

/**
 * Get lang name
 *
 * @return mixed
 */
function get_lang_name()
{
    return current_language()->name;
}

/**
 * Get active languages
 *
 * @return array
 */
function get_active_languages()
{
    $langs = [];
    foreach (Language::where('active',1)->get() as $language) {

        $langs[$language->code] = [
            'name' => $language->name,
        ];
    }
    return $langs;
}

/**
 * Language Translator
 *
 * @param $key
 * @param null $lang
 * @return string|array|null
 */
function lang($key)
{
    $allLanguages = File::directories(base_path('lang'));
    $trans_slug = Str::slug($key, '_');

    foreach ($allLanguages as $language) {
        $filePath = $language . '/' . 'lang.php';

        if (File::exists($filePath)) {
            $translations = include $filePath;
        } else {
            $translations = [];
        }

        if (!array_key_exists($trans_slug, $translations)) {
            $translations[$trans_slug] = $key;
            File::put($filePath, "<?php\n\nreturn " . var_export($translations, true) . ";\n");

            return $key;
        }
    }
    return trans('lang.' . $trans_slug, [], get_lang());
}

/**
 * Image Uploading
 * @param $file
 * @param $location
 * @param null $size
 * @param null $specificName
 * @param null $old
 * @return string
 */
function image_upload($file, $location, $size = null, $specificName = null, $old = null)
{
    /* Create folder if not exists */
    if (!File::exists($location)) {
        File::makeDirectory($location, 0775, true);
    }

    /* Generate file name */
    if (!empty($specificName)) {
        $filename = $specificName . '.' . $file->getClientOriginalExtension();
    } else {
        $filename = Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
    }

    /* Remove old file if exists */
    if (!empty($location.$old)) {
        remove_file($location.$old);
    }

    $image = Image::make($file);
    $width = $image->width();
    $height = $image->height();

    /* Resize the image if the size given */
    if (!empty($size)) {
        $newSize = explode('x', strtolower($size));
        if ($newSize[0] != $width && $newSize[1] != $height) {
            $image->resize($newSize[0], $newSize[1]);
        }
    }
    $image->save($location . $filename);

    return $filename;
}

/**
 * Remove file
 * @param $path
 * @return bool
 */
function remove_file($path)
{
    if (!file_exists($path)) {
        return true;
    }
    return File::delete($path);
}


/**
 * Date formats array
 * @return string[]
 */
function date_formats_array()
{
    $dateFormatsArray = [
        '0' => 'm-d-Y',
        '1' => 'd-m-Y',
        '2' => 'm/d/Y',
        '3' => 'd/m/Y',
        '4' => 'm-d-Y h:i A',
        '5' => 'd-m-Y h:i A',
        '6' => 'm/d/Y h:i A',
        '7' => 'd/m/Y h:i A',
        '8' => 'M d, Y',
        '9' => 'F d, Y',
        '10' => 'M d, Y h:i A',
        '11' => 'F d, Y h:i A',
        '12' => 'd M, Y',
        '13' => 'd F, Y',
        '14' => 'd M, Y h:i A',
        '15' => 'd F, Y h:i A',
    ];
    return $dateFormatsArray;
}

/**
 * Date format
 * @param $date
 * @return string
 */
function date_formating($date)
{
    Date::setLocale(get_lang());
    $format = Date::parse($date)->format(date_formats_array()[settings('date_format')]);
    return $format;
}

/**
 * @param $text
 * @param $chars_limit
 * @return string
 */
function text_shorting($text, $chars_limit)
{
    return Str::limit($text, $chars_limit);
}

/**
 * @param $number
 * @return mixed|string
 */
function format_number_count($number)
{
    $abbrevs = [12 => 'T', 9 => 'B', 6 => 'M', 3 => 'K', 0 => ''];
    foreach ($abbrevs as $exponent => $abbrev) {
        if (abs($number) >= pow(10, $exponent)) {
            $display = $number / pow(10, $exponent);
            $decimals = ($exponent >= 3 && round($display) < 100) ? 1 : 0;
            $number = number_format($display, $decimals) . $abbrev;
            break;
        }
    }
    return $number;
}

/**
 * @param Carbon $startDate
 * @param Carbon $endDate
 * @param string $format
 * @return \Illuminate\Support\Collection
 */
function chart_dates(Carbon $startDate, Carbon $endDate, $format = 'Y-m-d')
{
    $dates = collect();
    $startDate = $startDate->copy();
    for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
        $dates->put($date->format($format), 0);
    }
    return $dates;
}

/**
 * @param $id
 * @return string
 */
function hashid($id)
{
    return Hashids::encode($id);
}

/**
 * @param $id
 * @return array
 */
function unhashid($id)
{
    return Hashids::decode($id);
}

/**
 * @param $array
 * @return mixed
 */
function array_to_object($array)
{
    return json_decode(json_encode($array));
}

/**
 * @return string|null
 */
function google_analytics()
{
    $script = null;
    if (settings('google_analytics')->status) {
        $script = '<script async src="https://www.googletagmanager.com/gtag/js?id=' . settings('google_analytics')->measurement_id . '"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag("js", new Date());
          gtag("config", "' . settings('google_analytics')->measurement_id . '");
        </script>';
    }
    return $script;
}

/**
 * @return null
 */
function google_captcha()
{
    $script = null;
    if (settings('google_recaptcha')->status) {
        $script = NoCaptcha::renderJs(get_lang());
    }
    return $script;
}

/**
 * @return string|null
 */
function display_captcha()
{
    $script = null;
    if (settings('google_recaptcha')->status) {
        $script = '<div class="mb-3">' . app('captcha')->display() . '</div>';
    }
    return $script;
}

/**
 * @return string|null
 */
function tawk_to()
{
    $script = null;
    if (settings('tawk_to')->status) {
        $chat_link = settings('tawk_to')->chat_link;
        $chat_link = str_replace('https://tawk.to/chat/', '', $chat_link);

        $user_data = '';
        if($user = user_auth_info()){
            $user_data = "Tawk_API.visitor = {
                name: ".json_encode($user['name']) .",
                email: ".json_encode($user['email']) ."
            };";
        }

        $script = "<script type='text/javascript'>
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        $user_data
        (function(){
        var s1=document.createElement('script'),s0=document.getElementsByTagName('script')[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/$chat_link';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
        </script>";
    }

    return $script;
}

/**
 * Print switch button for admin
 *
 * @param $title
 * @param $id
 * @param false $checked
 * @param string $hint
 */
function quick_switch($title, $id, $checked = false, $hint = ''){
    $check = ($checked)? 'checked' : '';
    $tooltip = "";
    if(!empty($hint))
        $tooltip = '<i class="icon-feather-help-circle" title="'.$hint.'" data-tippy-placement="top"></i>';
    echo '<div class="form-group">
        <label class="form-label" for="'.$id.'">'.$title.' '.$tooltip.'</label>
        <div class="form-toggle-option">
            <div>
                <label for="'.$id.'">'.lang("Enable").'</label>
            </div>
            <div>
                <input type="hidden" name="'.$id.'" value="0">
                <label class="switch switch-sm">
                    <input name="'.$id.'" type="checkbox" id="'.$id.'" value="1" '.$check.'>
                    <span class="switch-state"></span>
                </label>
            </div>
        </div>
    </div>';
}

/**
 * Custom Toastr Alert
 *
 * @param $message
 * @param string $type
 */
function quick_alert($message, $type = 'success') {
    session()->flash('quick_alert_message', $message);
    session()->flash('quick_alert_type', $type);
}

/**
 * Success Toastr Alert
 *
 * @param $message
 */
function quick_alert_success($message) {
    quick_alert($message, 'success');
}

/**
 * Error Toastr Alert
 *
 * @param $message
 */
function quick_alert_error($message) {
    quick_alert($message, 'error');
}

/**
 * Info Toastr Alert
 *
 * @param $message
 */
function quick_alert_info($message) {
    quick_alert($message, 'info');
}
