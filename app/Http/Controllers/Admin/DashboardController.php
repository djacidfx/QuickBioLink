<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneratedImage;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $widget['total_earnings'] = Transaction::paid()->sum('total');
        $widget['total_users'] = User::all()->count();
        $widget['current_month_earnings'] = Transaction::whereMonth('created_at', Carbon::now()->month)->paid()->sum('total');
        $widget['current_month_users'] = User::whereMonth('created_at', Carbon::now()->month)->count();
        $transactions = Transaction::paid()->orderbyDesc('id')->limit(6)->get();
        $users = User::orderbyDesc('id')->limit(6)->get();

        /* Earning data */
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        $dates = chart_dates($startDate, $endDate);
        $getWeekEarnings = Transaction::paid()->where('created_at', '>=', Carbon::now()->startOfWeek())
            ->selectRaw('DATE(created_at) as date, SUM(total) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date');
        $getEarningsData = $dates->merge($getWeekEarnings);
        $earningsChartLabels = [];
        $earningsChartData = [];
        foreach ($getEarningsData as $key => $value) {
            $earningsChartLabels[] = Carbon::parse($key)->format('d M');
            $earningsChartData[] = number_format((float) $value, 2);
        }
        $suggestedMax = (max($earningsChartData) > 9) ? max($earningsChartData) + 2 : 10;
        $earningData = ['labels' => $earningsChartLabels, 'data' => $earningsChartData, 'max' => $suggestedMax];

        /* Users data */
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        $dates = chart_dates($startDate, $endDate);
        $usersRecord = User::where('created_at', '>=', Carbon::now()->startOfWeek())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');
        $usersRecordData = $dates->merge($usersRecord);
        $usersChartLabels = [];
        $usersChartData = [];
        foreach ($usersRecordData as $key => $value) {
            $usersChartLabels[] = Carbon::parse($key)->format('d M');
            $usersChartData[] = $value;
        }
        $suggestedMax = (max($usersChartData) > 9) ? max($usersChartData) + 2 : 10;
        $usersData = ['labels' => $usersChartLabels, 'data' => $usersChartData, 'max' => $suggestedMax];

        return view('admin.dashboard.index', [
            'widget' => $widget,
            'transactions' => $transactions,
            'users' => $users,
            'earningData' => $earningData,
            'usersData' => $usersData
        ]);
    }
}
