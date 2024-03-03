<?php

namespace App\Http\Controllers;

use App\Http\Methods\ReCaptchaValidation;
use App\Models\BlogArticle;
use App\Models\Faq;
use App\Models\Language;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Validator;
use App;
use Session;

class HomeController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    /**
     * Display the home page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $testimonials = Testimonial::limit(10)->get();
        $faqs = Faq::where('lang', get_lang())->limit(10)->get();
        $blogArticles = BlogArticle::where('lang', get_lang())->limit(4)->get();

        return view($this->activeTheme.'.home.index')->with(compact('testimonials','faqs', 'blogArticles'));
    }

    /**
     * Display the pricing page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function pricing()
    {
        $monthlyPlans = Plan::where('interval', 1)->get();
        $yearlyPlans = Plan::where('interval', 2)->get();
        return view($this->activeTheme.'.home.pricing', compact('monthlyPlans', 'yearlyPlans'));
    }

    /**
     * Display the faq page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function faqs()
    {
        $faqs = Faq::where('lang', get_lang())->paginate(20);
        return view($this->activeTheme.'.home.faqs', compact('faqs'));
    }

    /**
     * Display the static page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function page($slug)
    {
        $page = Page::where([['slug', $slug], ['lang', get_lang()]])->first();
        if ($page) {
            $page->increment('views');

            return view($this->activeTheme.'.home.page', compact('page'));
        } else {
            abort(404);
        }
    }

    /**
     * Display the contact page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function contact()
    {
        return view($this->activeTheme.'.home.contact');
    }

    /**
     * Handle contact requests
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function contactSend(Request $request)
    {
        if (!settings('smtp')->status || !settings('contact_email')) {
            quick_alert_error(lang('Email sending is disabled.'));
            return back();
        }

        $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'subject' => ['required', 'string', 'max:255'],
                'message' => ['required', 'string'],
            ] + ReCaptchaValidation::validate());

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back()->withInput();
        }

        try {
            $name = $request->name;
            $email = $request->email;
            $subject = $request->subject;
            $msg = nl2br($request->message);

            \Mail::send([], [], function ($message) use ($msg, $email, $subject, $name) {
                $message->to(settings('contact_email'))
                    ->from(env('MAIL_FROM_ADDRESS'), $name)
                    ->replyTo($email)
                    ->subject($subject)
                    ->html($msg);
            });

            quick_alert_success(lang('Thank you for contacting us.'));
            return back();

        } catch (\Exception$e) {
            quick_alert_error(lang('Email sending failed, please try again.'));
            return back();
        }
    }

    /**
     * Change the language
     *
     * @param $code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function localize($code)
    {
        $language = Language::where('code', $code)->firstOrFail();
        App::setLocale($language->code);
        Session::forget('locale');
        Session::put('locale', $language->code);

        return redirect()->back();
    }
}
