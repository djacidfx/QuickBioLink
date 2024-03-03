<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $total_earnings = Transaction::paid()->sum('total');
        $total_users = User::all()->count();
        $current_month_earnings = Transaction::whereMonth('created_at', Carbon::now()->month)->paid()->sum('total');
        $current_month_users = User::whereMonth('created_at', Carbon::now()->month)->count();

        $transactions = Transaction::paid()->orderbyDesc('id')->limit(6)->get();
        $users = User::orderbyDesc('id')->limit(6)->get();

        /* Earning Chart data */
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        $dates = chart_dates($startDate, $endDate);

        $earnings = Transaction::paid()
            ->where('created_at', '>=', Carbon::now()->startOfWeek())
            ->selectRaw('DATE(created_at) as date, SUM(total) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date');

        $getEarningsData = $dates->merge($earnings);
        $earningsLabels = [];
        $earningsData = [];
        foreach ($getEarningsData as $key => $value) {
            $earningsLabels[] = Carbon::parse($key)->format('d M');
            $earningsData[] = number_format((float) $value, 2);
        }
        $suggestedMax = (max($earningsData) > 9) ? max($earningsData) + 2 : 10;
        $earningData = ['labels' => $earningsLabels, 'data' => $earningsData, 'max' => $suggestedMax];

        /* Users Chart data */
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        $dates = chart_dates($startDate, $endDate);

        $usersChart = User::where('created_at', '>=', Carbon::now()->startOfWeek())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $usersRecordData = $dates->merge($usersChart);
        $usersLabels = [];
        $usersData = [];
        foreach ($usersRecordData as $key => $value) {
            $usersLabels[] = Carbon::parse($key)->format('d M');
            $usersData[] = $value;
        }
        $suggestedMax = (max($usersData) > 9) ? max($usersData) + 2 : 10;
        $usersData = ['labels' => $usersLabels, 'data' => $usersData, 'max' => $suggestedMax];

        return view('admin.dashboard.index', compact(
            'total_earnings',
            'total_users',
            'current_month_earnings',
            'current_month_users',
            'transactions',
            'users',
            'earningData',
            'usersData'
        ));
    }
}
