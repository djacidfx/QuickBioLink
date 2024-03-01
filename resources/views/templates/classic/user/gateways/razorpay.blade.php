@extends($activeTheme.'layouts.main')
@section('title', lang('Payment confirm', 'checkout'))
@section('content')
    <div class="container pt-100">
    <div class="my-50">
        <div class="row">
            <div class="col-lg-6 m-auto">
                <div class="title-head mb-30">
                    <h1 class="mb-0">{{ lang('Payment details', 'checkout') }}</h1>
                </div>
                <div class="card mb-30">
                    <table class="table table-bordered table-striped mb-20">
                        <tbody>
                            <tr>
                                <td class="p-3"><strong>{{ lang('Plan price', 'checkout') }}</strong></td>
                                <td class="p-3">{{ price_symbol_format($trx->details_before_discount->price) }}</td>
                            </tr>
                            <tr>
                                <td class="p-3"><strong>{{ lang('Tax', 'checkout') }}</strong></td>
                                <td class="p-3">{{ price_symbol_format($trx->details_before_discount->tax) }}</td>
                            </tr>
                            <tr>
                                <td class="p-3">
                                    <h6 class="mb-0"><strong>{{ lang('Subtotal', 'checkout') }}</strong></h6>
                                </td>
                                <td class="p-3">
                                    <h6 class="mb-0">
                                        <strong>{{ price_symbol_format($trx->details_before_discount->total) }}</strong>
                                    </h6>
                                </td>
                            </tr>
                            @if (!is_null($trx->coupon_id))
                                <tr>
                                    <td class="p-3"><strong>{{ lang('Discount', 'checkout') }}</strong>
                                        ({{ $trx->coupon->percentage }}%)</td>
                                    <td class="p-3 text-danger">
                                        - {{ price_symbol_format($trx->details_before_discount->total - $trx->details_after_discount->total) }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="p-3"><strong>{{ lang('Gateway fees', 'checkout') }}</strong></td>
                                <td class="p-3">
                                    + {{ price_symbol_format($trx->fees) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3">
                                    <h5 class="mb-0"><strong>{{ lang('Total', 'checkout') }}</strong></h5>
                                </td>
                                <td class="p-3">
                                    <h5 class="mb-0">
                                        <strong>{{ price_code_format($trx->total + $trx->fees) }}</strong>
                                    </h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <form action="{{ route('ipn.razorpay') }}" method="POST">
                                @csrf
                                <input type="hidden" name="checkout_id" value="{{ $trx->checkout_id }}">
                                <script src="https://checkout.razorpay.com/v1/checkout.js"
                                        @foreach ($details as $key => $value)
                                        data-{{ $key }}="{{ $value }}" @endforeach>
                                </script>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('subscription') }}"
                                class="button -secondary -lg w-100">{{ lang('Cancel Payment', 'checkout') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    @push('scripts_at_bottom')
        <script>
            "use strict";
            let razorpayPaymentButton = $('.razorpay-payment-button');
            razorpayPaymentButton.addClass('button -primary -lg w-100');
        </script>
    @endpush
@endsection

