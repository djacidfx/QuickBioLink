<?php

namespace App\Http\Controllers;

use App\Http\Methods\ReCaptchaValidation;
use App\Models\BlogArticle;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    public function index()
    {
        if(Auth::check()){
            $role = Auth::user()->role;
        }
        $testimonials = Testimonial::limit(10)->get();
        $faqs = Faq::where('lang', get_lang())->limit(10)->get();
        $blogArticles = BlogArticle::where('lang', get_lang())->limit(4)->get();
        return view($this->activeTheme.'.home.index')->with(compact('testimonials','faqs', 'blogArticles'));
    }

    public function pricing()
    {
        $monthlyPlans = Plan::monthly()->get();
        $yearlyPlans = Plan::yearly()->get();
        return view($this->activeTheme.'.home.pricing', ['monthlyPlans' => $monthlyPlans, 'yearlyPlans' => $yearlyPlans]);
    }

    public function faqs()
    {
        $faqs = Faq::where('lang', get_lang())->paginate(15);
        return view($this->activeTheme.'.home.faqs', ['faqs' => $faqs]);
    }

    public function page($slug)
    {
        $page = Page::where([['slug', $slug], ['lang', get_lang()]])->first();
        if ($page) {
            $page->increment('views');
            return view($this->activeTheme.'.home.page', ['page' => $page]);
        } else {
            return redirect()->route('home');
        }
    }

    public function contact()
    {
        return view($this->activeTheme.'.home.contact');
    }

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
            quick_alert_success(lang('Your message has been sent successfully'));
            return back();
        } catch (\Exception$e) {
            quick_alert_error(lang('Error on sending'));
            return back();
        }
    }
}
