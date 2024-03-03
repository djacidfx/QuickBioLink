<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanOption;
use Illuminate\Http\Request;
use Validator;

class PlanController extends Controller
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
                'position',
                'name',
                'price',
                'interval',
            );

            if(!empty($params['search']['value'])){
                $q = $params['search']['value'];
                $plans = Plan::where('name', 'like', '%' . $q . '%')
                    ->OrWhere('price', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }else{
                $plans = Plan::orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = Plan::count();
            foreach ($plans as $row) {
                $is_featured = $row->isFeatured() ? '<i class="fas fa-badge-check" data-tippy-placement="top" title="' . lang('Featured') . '"></i>' : '';

                if($row->interval == 1){
                    $interval_badge = '<span class="badge bg-success">'.lang('Monthly').'</span>';
                }else{
                    $interval_badge = '<span class="badge bg-secondary">'.lang('Yearly').'</span>';
                }

                if($row->isFree()){
                    $isFree_badge = '<span class="badge bg-success">'.lang('Free').'</span>';
                }else{
                    $isFree_badge = price_symbol_format($row->price);
                }
                $rows = array();
                $rows[] = '<td><i class="icon-feather-menu quick-reorder-icon"
                                       title="' . lang('Reorder') . '"></i> <span class="d-none">' . $row->id . '</span></td>';
                $rows[] = '<td>'.$row->name.' '.$is_featured.'</td>';
                $rows[] = '<td>'.$isFree_badge.'</td>';
                $rows[] = '<td>'.$interval_badge.'</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <a href="#" data-url="'.route('admin.plans.edit', $row->id).'" data-toggle="slidePanel" title="'.lang('Edit').'" class="btn btn-icon btn-default" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
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

        return view('admin.plans.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\PlanOption $PlanOption
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $PlanOption = PlanOption::where('active','1')->get();
        return view('admin.plans.create', compact('PlanOption'));
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
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:150'],
            'interval' => ['required', 'integer', 'min:1', 'max:2'],
            'price' => ['sometimes', 'required', 'numeric', 'regex:/^\d*(\.\d{2})?$/'],
            'biolink_limit' => ['required', 'integer', 'min:1'],
            'biopage_limit' => ['required', 'integer', 'min:1'],
        ]);

        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        if ($request->is_free) {
            $plan = Plan::free()->first();
            if ($plan) {
                $result = array('success' => false, 'message' => lang('Free plan already exists'));
                return response()->json($result, 200);
            }
            $request->price = 0;
        }

        $settings = array(
            'biolink_limit' => $request->biolink_limit,
            'biopage_limit' => $request->biopage_limit,
            'hide_branding' => $request->hide_branding,
        );

        $plan = Plan::create([
            'name' => $request->name,
            'short_description' => $request->short_description,
            'interval' => $request->interval,
            'price' => $request->price,
            'translations' => $request->translations,
            'settings' => $settings,
            'advertisements' => $request->advertisements,
            'custom_features' => $request->planoption,
            'is_free' => $request->is_free,
            'is_featured' => $request->is_featured,
        ]);
        if ($plan) {
            if ($request->is_featured) {
                Plan::where([['interval', $plan->interval], ['id', '!=', $plan->id]])->update(['is_featured' => 0]);
            }
            $result = array('success' => true, 'message' => lang('Created Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Plan  $plan
     */
    public function show(Plan $plan)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Plan  $plan
     * @param  \App\Models\Plan  $PlanOption
     * @return \Illuminate\Http\Response
     */
    public function edit(Plan $plan)
    {
        $PlanOption = PlanOption::where('active','1')->get();
        return view('admin.plans.edit', compact('plan', 'PlanOption'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:150'],
            'price' => ['sometimes', 'required', 'numeric', 'regex:/^\d*(\.\d{2})?$/'],
            'biolink_limit' => ['required', 'integer', 'min:1'],
            'biopage_limit' => ['required', 'integer', 'min:1'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        if ($request->is_free) {
            $freePlan = Plan::free()->first();
            if ($freePlan && $plan->id != $freePlan->id) {
                $result = array('success' => false, 'message' => lang('Free plan already exists'));
                return response()->json($result, 200);
            }
            $request->price = 0;
        }

        $settings = array(
            'biolink_limit' => $request->biolink_limit,
            'biopage_limit' => $request->biopage_limit,
            'hide_branding' => $request->hide_branding,
        );

        $update = $plan->update([
            'name' => $request->name,
            'short_description' => $request->short_description,
            'translations' => $request->translations,
            'settings' => $settings,
            'price' => $request->price,
            'advertisements' => $request->advertisements,
            'custom_features' => $request->planoption,
            'is_free' => $request->is_free,
            'is_featured' => $request->is_featured,
        ]);
        if ($update) {

            if ($request->is_featured) {
                Plan::where([['interval', $plan->interval], ['id', '!=', $plan->id]])->update(['is_featured' => 0]);
            }
            $result = array('success' => true, 'message' => lang('Updated Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Reorder the resources
     *
     * @param  \App\Models\Plan $plan
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request)
    {
        $position = $request->position;
        if (is_array($request->position)) {
            $count = 0;
            foreach($position as $id){
                $update = Plan::where('id',$id)->update([
                    'position' => $count,
                ]);

                $count++;
            }
            if ($update) {
                $result = array('success' => true, 'message' => lang('Updated Successfully'));
                return response()->json($result, 200);
            }
        }

        $result = array('success' => true, 'message' => lang('Updated Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Plan  $plan
     */
    public function destroy(Plan $plan)
    {
        abort(404);
    }

    /**
     * Remove the multiple resources from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $plans = Plan::whereIn('id',$ids)->get();
        foreach($plans as $plan){

            if ($plan->subscriptions->count() > 0) {
                $result = array('success' => false, 'message' => lang('Plan is assigned to subscriptions, delete them first.'));
                return response()->json($result, 200);
            }
            if ($plan->transactions->count() > 0) {
                $result = array('success' => false, 'message' => lang('Plan is assigned to  transactions, delete them first.'));
                return response()->json($result, 200);
            }
        }

        Plan::whereIn('id',$ids)->delete();

        $result = array('success' => true, 'message' => lang('Deleted Successfully'));
        return response()->json($result, 200);
    }
}
