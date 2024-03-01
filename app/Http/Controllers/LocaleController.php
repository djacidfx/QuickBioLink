<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\Models\Language;
use Session;

class LocaleController extends Controller
{
    public function localize($code)
    {
        if (!settings('include_language_code')) {
            $language = Language::where('code', $code)->firstOrFail();
            App::setLocale($language->code);
            Session::forget('locale');
            Session::put('locale', $language->code);
            return redirect()->back();
        }
    }
}
