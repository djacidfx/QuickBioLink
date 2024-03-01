<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\Plan;
use Illuminate\Http\Request;
use Validator;

class GatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gateways = PaymentGateway::all();
        return view('admin.gateways.index', ['gateways' => $gateways]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaymentGateway  $PaymentGateway
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentGateway $gateway)
    {
        return view('admin.gateways.edit', ['gateway' => $gateway]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PaymentGateway  $PaymentGateway
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentGateway $gateway)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'gateway_fees' => ['required', 'integer', 'min:0', 'max:100'],
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
        foreach ($request->credentials as $key => $value) {
            if (!array_key_exists($key, (array) $gateway->credentials)) {
                $result = array('success' => false, 'message' => admin_lang('Invalid Credentials'));
                return response()->json($result, 200);
            }
        }
        if ($request->get('status')) {
            foreach ($request->credentials as $key => $value) {
                if (empty($value)) {
                    $result = array('success' => false, 'message' => str_replace('_', ' ', $key) .' '. admin_lang('is required.'));
                    return response()->json($result, 200);
                }
            }
            $request->status = 1;
        } else {
            $plans = Plan::notFree()->get();
            if ($plans->count() > 0) {
                $checkPaymentMethods = PaymentGateway::where([['id', '!=', $gateway->id], ['status', 1]])->get();
                if ($checkPaymentMethods->count() < 1) {
                    $result = array('success' => false, 'message' => admin_lang('Plans require at least one payment method to work you cannot disable them all.'));
                    return response()->json($result, 200);
                }
            }
            $request->status = 0;
        }
        if (!is_null($gateway->test_mode)) {
            $request->test_mode = $request->get('test_mode');
        } else {
            $request->test_mode = null;
        }
        if ($request->has('logo') && $request->logo != null) {
            $logo = image_upload($request->file('logo'), 'storage/payments/', '300x100', null, $gateway->logo);
        } else {
            $logo = $gateway->logo;
        }
        if ($logo) {
            $update = $gateway->update([
                'name' => $request->name,
                'logo' => $logo,
                'fees' => $request->gateway_fees,
                'test_mode' => $request->test_mode,
                'credentials' => $request->credentials,
                'status' => $request->status,
            ]);
            if ($update) {
                $result = array('success' => true, 'message' => admin_lang('Updated Successfully'));
                return response()->json($result, 200);
            }
        }
    }

}
