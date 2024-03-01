<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemInfoController extends Controller
{
    public function index()
    {
        $system['application']['name'] = env('APP_NAME');
        $system['application']['version'] = env('APP_VERSION');
        $system['application']['laravel'] = app()->version();
        $system['application']['timezone'] = config('app.timezone');
        $system['server'] = $_SERVER;
        $system['server']['php'] = phpversion();
        $system = json_decode(json_encode($system));
        return view('admin.system-info.index', ['system' => $system]);
    }

    public function cache(Request $request)
    {
        Artisan::call('optimize:clear');
        remove_file(storage_path('logs/laravel.log'));
        quick_alert_success(admin_lang('Cache Cleared Successfully'));
        return back();
    }
}
