<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $unviewedTransactions = Transaction::where('is_viewed', 0)->whereIn('status', [2, 3])->get();
        if ($unviewedTransactions->count() > 0) {
            foreach ($unviewedTransactions as $unviewedTransaction) {
                $unviewedTransaction->is_viewed = true;
                $unviewedTransaction->save();
            }
        }

        if ($request->ajax()) {
            $params = $columns = $order = $totalRecords = $data = array();
            $params = $request;

            //define index of column
            $columns = array(
                'id',
                'user_id',
                'plan_id',
                'total',
                '',
                'type',
                'created_at'
            );

            if (!empty($params['search']['value'])) {
                $q = $params['search']['value'];
                $transaction = Transaction::with(['user', 'plan', 'gateway'])
                    ->whereNotIn('status', [0, 1])
                    ->where('id', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            } else {
                $transaction = Transaction::with(['user', 'plan', 'gateway'])
                    ->whereNotIn('status', [0, 1])
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = Transaction::whereNotIn('status', [0, 1])->count();
            foreach ($transaction as $row) {
                if ($row->gateway) {
                    $gateway_badge = '<span class="badge bg-secondary">' . $row->gateway->name . '</span>';
                } else {
                    $gateway_badge = '<span>-</span>';
                }

                if ($row->type == 1) {
                    $transation_type_badge = '<span class="badge bg-primary">' . admin_lang('Subscribe') . '</span>';
                } elseif ($row->type == 2) {
                    $transation_type_badge = '<span class="badge bg-success">' . admin_lang('Renew') . '</span>';
                } elseif ($row->type == 3) {
                    $transation_type_badge = '<span class="badge bg-info">' . admin_lang('Upgrade') . '</span>';
                } elseif ($row->type == 4) {
                    $transation_type_badge = '<span class="badge bg-warning">' . admin_lang('Downgrade') . '</span>';
                }

                if ($row->status == 0) {
                    $status_badge = '<span class="badge bg-danger">' . admin_lang('Unpaid') . '</span>';
                } elseif ($row->status == 1) {
                    $status_badge = '<span class="badge bg-info">' . admin_lang('Pending') . '</span>';
                } elseif ($row->status == 2) {
                    $status_badge = '<span class="badge bg-success">' . admin_lang('Paid') . '</span>';
                } elseif ($row->status == 3) {
                    $status_badge = '<span class="badge bg-warning">' . admin_lang('Cancelled') . '</span>';
                }

                $invoice_button = '';
                if ($row->total > 0) {
                    $invoice_button = '<a href="' . route('invoice', $row->id) . '" title="' . admin_lang('Invoice') . '" class="btn btn-default btn-icon ms-1" data-tippy-placement="top" target="_blank"><i class="icon-feather-paperclip"></i></a>';
                }

                $rows = array();
                $rows[] = '<td>' . $row->id . '</td>';
                $rows[] = '<td><a class="text-body" href="'. route('admin.users.edit', $row->user->id).'">' . $row->user->name . '</a></td>';
                $rows[] = '<td>' . $row->plan->name . '</td>';
                $rows[] = '<td>' . price_symbol_format($row->total) . '</td>';
                $rows[] = '<td>' . $gateway_badge . '</td>';
                $rows[] = '<td>' . $transation_type_badge . '</td>';
                $rows[] = '<td>' . $status_badge . '</td>';
                $rows[] = '<td>' . date_formating($row->created_at) . '</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                <button data-url=" ' . route('admin.transactions.edit', $row->id) . '" data-toggle="slidePanel" title="' . admin_lang('Details') . '" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-list"></i></button>
                                   '.$invoice_button.'
                                </div>
                            </td>';
                $rows[] = '<td>
                                <div class="checkbox">
                                <input type="checkbox" id="check_' . $row->id . '" value="' . $row->id . '" class="quick-check">
                                <label for="check_' . $row->id . '"><span class="checkbox-icon"></span></label>
                            </div>
                           </td>';
                $rows['DT_RowId'] = $row->id;
                $data[] = $rows;
            }

            $json_data = array(
                "draw" => intval($params['draw']),
                "recordsTotal" => intval($totalRecords),
                "recordsFiltered" => intval($totalRecords),
                "data" => $data   // total data array
            );
            return response()->json($json_data, 200);
        }

        return view('admin.transactions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        abort_if($transaction->isPending() || $transaction->isUnpaid(), 404);
        return view('admin.transactions.edit', ['transaction' => $transaction]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->status != 2) {
            quick_alert_error(admin_lang('Transaction cannot be canceled'));
            return back();
        }
        $updateTransaction = $transaction->update(['status' => 3]);
        if ($updateTransaction) {
            quick_alert_success(admin_lang('Transaction Canceled Successfully'));
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        quick_alert_success(admin_lang('Deleted Successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $sql = Transaction::whereIn('id', $ids)->delete();
        if ($sql) {
            $result = array('success' => true, 'message' => admin_lang('Deleted Successfully'));
            return response()->json($result, 200);
        }
    }
}
