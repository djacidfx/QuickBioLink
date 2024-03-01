<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class CouponController extends Controller
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
                'user_id',
                'plan_id',
                'created_at',
                'expiry_at',
                'status'
            );

            if(!empty($params['search']['value'])){
                $q = $params['search']['value'];
                $coupon = Coupon::with('plan')
                    ->where('id', 'like', '%' . $q . '%')
                    ->OrWhere('user_id', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }else{
                $coupon = Coupon::with('plan')
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();

            }

            $totalRecords = Coupon::count();
            foreach ($coupon as $row) {
                if (!is_null($row->plan_id)){
                    $plan_badge = '<span class="badge bg-danger">'.$row->plan->name.'</span>';
                } else{
                    $plan_badge = '<span class="badge bg-black">'.admin_lang('All plans').'</span>';
                }

                if ($row->action_type == 0){
                    $status_badge = '<span class="badge bg-black">'.admin_lang('All actions').'</span>';
                }elseif ($row->action_type == 1){
                    $status_badge = '<span class="badge bg-success">'.admin_lang('Subscribing').'</span>';
                }elseif ($row->action_type == 2){
                    $status_badge = '<span class="badge bg-info">'.admin_lang('Renewal').'</span>';
                }elseif ($row->action_type == 3){
                    $status_badge = '<span class="badge bg-warning text-dark">'.admin_lang('Upgrade').'</span>';
                }else{
                    $status_badge = "";
                }

                if (!$row->isExpiry()){
                    $expiry_badge = date_formating($row->expiry_at);
                } else{
                    $expiry_badge = '<span class="badge bg-danger">'.admin_lang('Expired').'</span>';
                }

                $rows = array();
                $rows[] = '<td>'.$row->id.'</td>';
                $rows[] = '<td><strong>'.$row->code.'</strong></td>';
                $rows[] = '<td>'.$row->percentage.'% '.admin_lang('OFF').'</td>';
                $rows[] = '<td>'.$row->limit.'</td>';
                $rows[] = '<td>'.$plan_badge.'</td>';
                $rows[] = '<td>'.$status_badge.'</td>';
                $rows[] = '<td>'.$expiry_badge.'</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <a href="#" data-url="'.route('admin.coupons.edit', $row->id).'" data-toggle="slidePanel" title="'.admin_lang('Edit').'" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                                </div>
                            </td>';
                $rows[] = '<td>
                                <div class="checkbox">
                                <input type="checkbox" id="check_'.$row->id.'" value="'.$row->id.'" class="quick-check">
                                <label for="check_'.$row->id.'"><span class="checkbox-icon"></span></label>
                            </div>
                           </td>';
                $rows['DT_RowId'] = $row->id;
                $data[] = $rows;
            }

            $json_data = array(
                "draw"            => intval( $params['draw'] ),
                "recordsTotal"    => intval( $totalRecords ),
                "recordsFiltered" => intval($totalRecords),
                "data"            => $data   // total data array
            );
            return response()->json($json_data, 200);
        }

        return view('admin.coupons.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $plans = Plan::notFree()->get();
        return view('admin.coupons.create', ['plans' => $plans]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'regex:/^[a-zA-Z0-9]*$/', 'min:3', 'max:20', 'unique:coupons'],
            'percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'limit' => ['required', 'integer', 'min:1'],
            'plan' => ['required', 'integer', 'min:0'],
            'action_type' => ['required', 'integer', 'min:0', 'max:3', 'max:3'],
            'expiry_at' => ['required', 'required'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }
        if ($request->plan != 0) {
            $plan = Plan::where('id', $request->plan)->notFree()->first();
            if (is_null($plan)) {
                $result = array('success' => false, 'message' => admin_lang('Invalid plan'));
                return response()->json($result, 200);
            }
            $request->plan = $plan->id;
        } else {
            $request->plan = null;
        }
        if (Carbon::parse($request->expiry_at) < Carbon::now()) {
            $result = array('success' => false, 'message' => admin_lang('Invalid expiry date'));
            return response()->json($result, 200);
        } elseif (Carbon::now()->addMinutes(5) > Carbon::parse($request->expiry_at)) {
            $result = array('success' => false, 'message' => admin_lang('Expiry date must be 5 minutes minimum'));
            return response()->json($result, 200);
        }
        $request->expiry_at = Carbon::parse($request->expiry_at);
        $create = Coupon::create([
            'code' => strtoupper($request->code),
            'percentage' => $request->percentage,
            'limit' => $request->limit,
            'plan_id' => $request->plan,
            'action_type' => $request->action_type,
            'expiry_at' => $request->expiry_at,
        ]);
        if ($create) {
            $result = array('success' => true, 'message' => admin_lang('Created Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        $plans = Plan::notFree()->get();
        return view('admin.coupons.edit', ['coupon' => $coupon, 'plans' => $plans]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validator = Validator::make($request->all(), [
            'limit' => ['required', 'integer', 'min:1'],
            'expiry_at' => ['required', 'required'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }
        if (Carbon::parse($request->expiry_at) != $coupon->expiry_at) {
            if (Carbon::parse($request->expiry_at) < Carbon::now()) {
                $result = array('success' => false, 'message' => admin_lang('Invalid expiry date'));
                return response()->json($result, 200);
            } elseif (Carbon::now()->addMinutes(5) > Carbon::parse($request->expiry_at)) {
                $result = array('success' => false, 'message' => admin_lang('Expiry date must be 5 minutes minimum'));
                return response()->json($result, 200);
            }
            $request->expiry_at = Carbon::parse($request->expiry_at);
        } else {
            $request->expiry_at = $coupon->expiry_at;
        }
        $update = $coupon->update([
            'limit' => $request->limit,
            'expiry_at' => $request->expiry_at,
        ]);
        if ($update) {
            $result = array('success' => true, 'message' => admin_lang('Updated Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        quick_alert_success(admin_lang('Deleted successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $sql = Coupon::whereIn('id',$ids)->delete();
        if($sql){
            $result = array('success' => true, 'message' => admin_lang('Deleted Successfully'));
            return response()->json($result, 200);
        }
    }
}
