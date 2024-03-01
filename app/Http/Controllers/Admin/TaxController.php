<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Tax;
use Illuminate\Http\Request;
use Validator;

class TaxController extends Controller
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
                'title',
                'percentage'
            );

            if (!empty($params['search']['value'])) {
                $q = $params['search']['value'];
                $tax = Tax::where('title', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            } else {
                $tax = Tax::orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = Tax::count();
            foreach ($tax as $row) {
                if($row->country_id == null){
                    $country_name = admin_lang('All Countries');
                }else{
                    $country_name = $row->country->name;
                }
                $rows = array();
                $rows[] = '<td><div class="d-flex align-items-center">' . $country_name . '</div></td>';
                $rows[] = '<td><p>' . $row->title . '</p></td>';
                $rows[] = '<td><p>' . $row->percentage . '%</p></td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <button data-url=" ' . route('admin.taxes.edit', $row->id) . '" data-toggle="slidePanel" title="' . admin_lang('Edit') . '" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></button>
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
                "data" => $data // total data array
            );
            return response()->json($json_data, 200);
        }

        return view('admin.taxes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.taxes.create');
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
            'title' => ['required', 'max:255'],
            'description' => ['required', 'max:255'],
            'country_id' => ['required', 'integer', 'unique:taxes'],
            'percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }
        if ($request->country_id != 0) {
            $country = Country::find($request->country_id);
            if ($country == null) {
                $result = array('success' => false, 'message' => admin_lang('Country not found'));
                return response()->json($result, 200);
            }
        } else {
            $tax = Tax::whereNull('country_id')->first();
            if (!is_null($tax)) {
                $result = array('success' => false, 'message' => admin_lang('Tax for all countries already exists'));
                return response()->json($result, 200);
            }
            $request->country_id = null;
        }

        $createTax = Tax::create([
            'title' => $request->title,
            'description' => $request->description,
            'country_id' => $request->country_id,
            'percentage' => $request->percentage,
            'type' => "Inclusive",
            'billing' => "Personal"
        ]);
        if ($createTax) {
            $result = array('success' => true, 'message' => admin_lang('Created Successfully'));
            return response()->json($result, 200);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function show(Tax $tax)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function edit(Tax $tax)
    {
        return view('admin.taxes.edit', ['tax' => $tax]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tax $tax)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'max:255'],
            'description' => ['required', 'max:255'],
            'country_id' => ['required', 'integer', 'unique:taxes,country_id,' . $tax->id],
            'percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }
        if ($request->country_id != 0) {
            $country = Country::find($request->country_id);
            if ($country == null) {
                $result = array('success' => false, 'message' => admin_lang('Country not found'));
                return response()->json($result, 200);
            }
        } else {
            if (!is_null($tax->country_id)) {
                $existTax = Tax::whereNull('country_id')->first();
                if ($existTax) {
                    $result = array('success' => false, 'message' => admin_lang('Tax for all countries already exists'));
                    return response()->json($result, 200);
                }
            }
            $request->country_id = null;
        }
        $updateTax = $tax->update([
            'title' => $request->title,
            'description' => $request->description,
            'country_id' => $request->country_id,
            'percentage' => $request->percentage,
        ]);
        if ($updateTax) {
            quick_alert_success(admin_lang('Updated Successfully'));
            $result = array('success' => true, 'message' => admin_lang('Updated Successfully'));
            return response()->json($result, 200);
        }
    }

    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $admins = Tax::whereIn('id', $ids)->get();
        Tax::whereIn('id', $ids)->delete();
        $result = array('success' => true, 'message' => admin_lang('Deleted Successfully'));
        return response()->json($result, 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tax $tax)
    {
        $tax->delete();
        quick_alert_success(admin_lang('Deleted Successfully'));
        return back();
    }
}
