<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Str;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    protected function user()
    {
        return user_auth_info();
    }

    public function transactions()
    {
        $transactions = Transaction::where('user_id', $this->user()->id)->whereIn('status', [2, 3])->orderbyDesc('id')->get();
        return view($this->activeTheme.'.user.transactions', ['transactions' => $transactions]);
    }

    public function invoice(Transaction $transaction)
    {
        abort_if($transaction->isPending() || $transaction->isUnpaid() || $transaction->total == 0, 404);

        if($this->user()->user_type != 'admin' && $transaction->user_id != $this->user()->id){
            abort(404);
        }

        return view('admin.transactions.invoice', ['transaction' => $transaction]);
    }
}
