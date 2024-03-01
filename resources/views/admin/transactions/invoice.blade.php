<!DOCTYPE html>
<html lang="{{ get_lang() }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ lang('Invoice', 'account') . ' #'. $transaction->id}}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/brand/'.$settings->media->favicon) }}">
    <meta name="theme-color" content="{{ $settings->colors->primary_color }}">
    <style>
        :root {
            --theme-color: {{ $settings->colors->primary_color }};
        }
    </style>
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/invoice.css') }}">
</head>
<body>
<!-- Print Button -->
<div class="print-button-container">
    <a href="javascript:window.print()" class="print-button">{{lang('Print this invoice', 'account')}}</a>
</div>
<!-- Invoice -->
<div id="invoice">
    <!-- Header -->
    <div class="row">
        <div class="col-xl-6">
            <div id="logo">
                <img src="{{ asset('storage/brand/'.$settings->media->dark_logo) }}" alt="{{ @$settings->site_title }}">
            </div>
        </div>
        <div class="col-xl-6">
            <p id="details">
                <strong>{{lang('Invoice', 'account')}}
                    :</strong> {{$settings->invoice_billing->invoice_number_prefix ?? 'INV-'}}{{$transaction->id}} <br>
                <strong>{{lang('Date', 'account')}}:</strong> {{ date_formating($transaction->created_at) }} </p>
        </div>
    </div>


    <!-- Client & Supplier -->
    <div class="row">
        <div class="col-xl-12">
            <h2>{{lang('Invoice', 'account')}}</h2>
        </div>
        <div class="col-md-6">
            <h3>{{lang('Supplier', 'account')}}</h3>
            <p>
                @if(!empty($settings->invoice_billing->name))
                    <strong>{{lang('Name', 'account')}}</strong> {{$settings->invoice_billing->name}}<br>
                @endif
                @if(!empty($settings->invoice_billing->address))
                    <strong>{{lang('Address', 'account')}}</strong> {{$settings->invoice_billing->address}}<br>
                @endif
                @if(!empty($settings->invoice_billing->city))
                    <strong>{{lang('City', 'account')}}</strong> {{$settings->invoice_billing->city}}<br>
                @endif
                @if(!empty($settings->invoice_billing->state))
                    <strong>{{lang('State', 'account')}}</strong> {{$settings->invoice_billing->state}}<br>
                @endif
                @if(!empty($settings->invoice_billing->zipcode))
                    <strong>{{lang('Zip Code', 'account')}}</strong> {{$settings->invoice_billing->zipcode}}<br>
                @endif
                @if(!empty($settings->invoice_billing->country))
                    <strong>{{lang('Country', 'account')}}</strong> {{$settings->invoice_billing->country}}<br>
                @endif
                @if(!empty($settings->invoice_billing->tax_type) && !empty($settings->invoice_billing->tax_id))
                    <strong>{{$settings->invoice_billing->tax_type}}</strong> {{$settings->invoice_billing->tax_id}}<br>
                @endif
            </p>
        </div>
        <div class="col-md-6">
            <h3>{{lang('Customer', 'account')}}</h3>
            <p>
                <strong>{{lang('Name', 'account')}}</strong> {{ $transaction->user->name }}<br>
                <strong>{{lang('Address', 'account')}}</strong> {{ $transaction->billing_address->address }}<br>
                <strong>{{lang('City', 'account')}}</strong> {{ $transaction->billing_address->city }}<br>
                <strong>{{lang('State', 'account')}}</strong> {{ $transaction->billing_address->state }}<br>
                <strong>{{lang('Zip Code', 'account')}}</strong> {{ $transaction->billing_address->zip }}<br>
                <strong>{{lang('Country', 'account')}}</strong> {{ $transaction->billing_address->country }}<br>
            </p>
        </div>
    </div>
    <!-- Invoice -->
    <div class="row">
        <div class="col-xl-12">
            <table>
                <tr>
                    <th>{{lang('Item', 'account')}}</th>
                    <th>{{lang('Amount', 'account')}}</th>
                </tr>
                <tr>
                    <td>{{ $transaction->plan->name }}<br><small>{{lang('Membership Plan', 'account')}}</small></td>
                    <td>{{ price_symbol_format($transaction->details_before_discount->price) }}</td>
                </tr>
                @if ($transaction->coupon)
                    <tr>
                        <td>
                            {{lang('Discount', 'account')}}({{ $transaction->coupon->percentage }}%)
                            <br><small>{{lang('Coupon', 'account') }} <strong>{{$transaction->coupon->code}}</strong></small>
                        </td>
                        <td>-{{ price_symbol_format($transaction->details_before_discount->price - $transaction->price) }}</td>
                    </tr>
                    <tr>
                        <td>{{lang('Subtotal', 'account')}}</td>
                        <td>{{ price_symbol_format($transaction->details_after_discount->price) }}</td>
                    </tr>
                @endif
                @if ($transaction->details_before_discount->tax != 0)
                    <tr>
                        <td>{{lang('Taxes', 'account')}}</td>
                        <td>
                            @if ($transaction->coupon)
                                +{{ price_symbol_format($transaction->details_after_discount->tax) }}
                            @else
                                +{{ price_symbol_format($transaction->details_before_discount->tax) }}
                            @endif
                        </td>
                    </tr>
                @endif
                @if ($transaction->gateway)
                    <tr>
                        <td>{{lang('Gateway Fees', 'account')}}</td>
                        <td>+{{ price_symbol_format($transaction->fees) }}</td>
                    </tr>
                @endif
            </table>
            <table id="totals">
                <tr>
                    <th>{{lang('Total', 'account')}}
                        @if ($transaction->gateway)
                            <br>
                            <small>{{lang('Paid via', 'account')}} {{ $transaction->gateway->name }}</small>
                        @endif
                    </th>
                    <th><span>{{ price_symbol_format($transaction->total) }}</span></th>
                </tr>
            </table>
        </div>
    </div>
    <!-- Footer -->
    <div class="row">
        <div class="col-xl-12">
            <ul id="footer">
                <li><span>{{url('/')}}</span></li>
                <li>{{@$settings->invoice_billing->email}}</li>
                <li>{{@$settings->invoice_billing->phone}}</li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
