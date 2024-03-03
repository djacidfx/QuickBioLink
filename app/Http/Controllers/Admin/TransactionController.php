<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        if ($request->ajax()) {
            $params = $columns = $order = $totalRecords = $data = array();
            $params = $request;

            //define index of column
            $columns = array(
                'id',
                'plan_id',
                'user_id',
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
                    $transation_type_badge = '<span class="badge bg-primary">' . lang('Subscribe') . '</span>';
                } elseif ($row->type == 2) {
                    $transation_type_badge = '<span class="badge bg-success">' . lang('Renew') . '</span>';
                } elseif ($row->type == 3) {
                    $transation_type_badge = '<span class="badge bg-info">' . lang('Upgrade') . '</span>';
                } elseif ($row->type == 4) {
                    $transation_type_badge = '<span class="badge bg-warning">' . lang('Downgrade') . '</span>';
                }

                if ($row->status == 0) {
                    $status_badge = '<span class="badge bg-danger">' . lang('Unpaid') . '</span>';
                } elseif ($row->status == 1) {
                    $status_badge = '<span class="badge bg-info">' . lang('Pending') . '</span>';
                } elseif ($row->status == 2) {
                    $status_badge = '<span class="badge bg-success">' . lang('Paid') . '</span>';
                } elseif ($row->status == 3) {
                    $status_badge = '<span class="badge bg-warning">' . lang('Cancelled') . '</span>';
                }

                $invoice_button = '';
                if ($row->total > 0) {
                    $invoice_button = '<a href="' . route('invoice', $row->id) . '" title="' . lang('Invoice') . '" class="btn btn-default btn-icon ms-1" data-tippy-placement="top" target="_blank"><i class="icon-feather-paperclip"></i></a>';
                }

                $rows = array();
                $rows[] = '<td>' . $row->id . '</td>';
                $rows[] = '<td>' . $row->plan->name . '</td>';
                $rows[] = '<td><a class="text-body" href="'. route('admin.users.edit', $row->user->id).'">' . $row->user->name . '</a></td>';
                $rows[] = '<td>' . price_symbol_format($row->total) . '</td>';
                $rows[] = '<td>' . $gateway_badge . '</td>';
                $rows[] = '<td>' . $transation_type_badge . '</td>';
                $rows[] = '<td>' . $status_badge . '</td>';
                $rows[] = '<td>' . date_formating($row->created_at) . '</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                <button data-url=" ' . route('admin.transactions.edit', $row->id) . '" data-toggle="slidePanel" title="' . lang('Details') . '" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-list"></i></button>
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

        /* Mark transactions as viewed */
        $unviewed = Transaction::where('is_viewed', 0)
            ->whereIn('status', [Transaction::STATUS_PAID, Transaction::STATUS_CANCELLED])
            ->get();
        foreach ($unviewed as $transaction) {
            $transaction->is_viewed = true;
            $transaction->save();
        }

        return view('admin.transactions.index');
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
        return view('admin.transactions.edit', compact('transaction'));
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
        if ($transaction->status != Transaction::STATUS_PAID) {
            quick_alert_error(lang('Can not cancel this transaction.'));
            return back();
        }

        $update = $transaction->update(['status' => Transaction::STATUS_CANCELLED]);
        if ($update) {
            quick_alert_success(lang('Transaction Canceled'));
            return back();
        }
    }

    /**
     * Remove multiple resources from storage.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $sql = Transaction::whereIn('id', $ids)->delete();
        if ($sql) {
            $result = array('success' => true, 'message' => lang('Deleted Successfully'));
            return response()->json($result, 200);
        }
    }
}
