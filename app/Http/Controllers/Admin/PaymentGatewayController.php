<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Validator;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gateways = PaymentGateway::all();
        return view('admin.gateways.index', compact('gateways'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\PaymentGateway $PaymentGateway
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentGateway $gateway)
    {
        return view('admin.gateways.edit', compact('gateway'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PaymentGateway $gateway
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentGateway $gateway)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'gateway_fees' => ['required', 'integer', 'min:0', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'status' => ['required', 'boolean'],
        ]);

        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        if (!is_null($gateway->test_mode)) {
            $request->test_mode = $request->get('test_mode');
        } else {
            $request->test_mode = null;
        }

        if ($request->has('logo') && $request->logo != null) {
            $logo = image_upload($request->file('logo'), 'storage/payments/', '200x77', null, $gateway->logo);
        } else {
            $logo = $gateway->logo;
        }

        if ($logo) {
            $update = $gateway->update([
                'status' => $request->status,
                'logo' => $logo,
                'name' => $request->name,
                'fees' => $request->gateway_fees,
                'test_mode' => $request->test_mode,
                'credentials' => $request->credentials,
            ]);
            if ($update) {
                $result = array('success' => true, 'message' => lang('Updated Successfully'));
                return response()->json($result, 200);
            }
        }
    }

}
