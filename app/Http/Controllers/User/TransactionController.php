<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Str;

class TransactionController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    /**
     * Display the page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $transactions = Transaction::where('user_id', user_auth_info()->id)->whereIn('status', [2, 3])->orderbyDesc('id')->get();
        return view($this->activeTheme.'.user.transactions', ['transactions' => $transactions]);
    }

    /**
     * Display the invoice
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function invoice(Transaction $transaction)
    {
        abort_if($transaction->isPending() || $transaction->isUnpaid() || $transaction->total == 0, 404);

        if(user_auth_info()->user_type != 'admin' && $transaction->user_id != user_auth_info()->id){
            abort(404);
        }

        return view('admin.transactions.invoice', ['transaction' => $transaction]);
    }
}
