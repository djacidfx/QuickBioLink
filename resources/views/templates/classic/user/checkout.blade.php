@extends($activeTheme.'layouts.main')
@section('title', lang('Checkout', 'checkout'))
@section('content')
    <section class="page-banner-area theme-gradient-3 pt-170">
        <div class="container">
            <div class="row wow fadeInUp" data-wow-delay="300ms">
                <div class="col-md-10 col-xl-8 mx-auto">
                    <div class="d-flex flex-column align-items-center">
                        <h2>{{ lang('Checkout', 'checkout')  }}</h2>
                        <p>{{ lang('Experience the joy of hassle-free payments.', 'checkout')  }}</p>
                        <ol class="breadcrumb text-grey-2">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ lang('Home', 'pages') }}</a></li>
                            <li class="breadcrumb-item active text-dark-1" aria-current="page">{{ lang('Checkout', 'checkout')  }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="our-subscription pt-70 pb-100">
        <div class="container">
            <!-- / Important Notice-->
            <div class="row wow fadeIn">
                <div class="col-12">
                    @if ($transaction->type == 4)
                        <div class="blockquote-style1 mb-60">
                            <blockquote class="blockquote">
                                <h5 class="quote-title"><i class="fa-regular fa-circle-question me-2"></i>
                                    {{ lang('Important Notice !', 'checkout') }}
                                </h5>
                                <p class="fst-italic font-15 text-dark-2">{{ lang('downgrading notice', 'checkout') }}</p>
                            </blockquote>
                        </div>
                    @elseif($transaction->type == 3)
                        <div class="blockquote-style1 mb-60">
                            <blockquote class="blockquote">
                                <h5 class="quote-title"><i class="fa-regular fa-circle-question me-2"></i>
                                    {{ lang('Important Notice !', 'checkout') }}</h5>
                                <p class="fst-italic font-15 text-dark-2">{{ lang('upgrading notice', 'checkout') }}</p>
                            </blockquote>
                        </div>
                    @endif
                </div>
            </div>
            <!-- / Important Notice-->

            <!-- / Coupon Code-->
            <div class="row wow fadeIn">
                <div class="col-12">
                    @if (!$transaction->plan->isFree())
                        @if (!$transaction->coupon)
                            <div class="coupon-toggle">
                                <div id="accordion" class="accordion">
                                    <div class="card pb-0">
                                        <div class="card-header pb-30" id="headingOne">
                                            <div class="card-title mb-0">
                                                <span><i class="fa fa-window-maximize"></i> {{ lang('Have a coupon?', 'checkout') }}</span>
                                                <button class="accordion-toggle collapsed text-decoration -underline-2 ml-8"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                        aria-expanded="false" aria-controls="collapseOne">{{ lang('Click here to enter your code', 'checkout') }}
                                                </button>
                                            </div>
                                        </div>
                                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                             data-bs-parent="#accordion">
                                            <div class="card-body border-top pt-20 pb-30">
                                                <p>{{ lang('If you have a coupon code, please apply it below.', 'checkout') }}</p>
                                                <div class="coupon-code-input w-50">
                                                    <form action="{{ route('checkout.coupon.apply', $transaction->checkout_id) }}"
                                                          method="POST">
                                                        @csrf
                                                        <div class="input-group">
                                                            <input type="text" name="coupon_code" class="form-control text-field"
                                                                   placeholder="{{ lang('Enter coupon code', 'checkout') }}" max="20" value="{{ old('coupon_code') }}" required>
                                                            <button class="button -primary transform-none h-48-px">{{ lang('Apply', 'checkout') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center alert alert-primary mb-0 py-2">
                                <span>
                                    <i class="fa-solid fa-ticket me-2"></i> {{ lang('Coupon code', 'checkout') }} <span class="fw-bold">{{ $transaction->coupon->code }}</span> {{ lang('Applied', 'checkout') }}.
                                </span>
                                <form action="{{ route('checkout.coupon.remove', $transaction->checkout_id) }}" method="POST">
                                    @csrf
                                    <button class="button -danger transform-none h-48-px action-confirm">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            <!-- / Coupon Code-->

            <div class="row wow fadeInUp">


                <h4 class="my-30">{{ lang('Checkout', 'checkout') }}</h4>

                <div class="col-12 col-md-7 col-lg-8">
                    <form id="checkoutForm" action="{{ route('checkout.process', $transaction->checkout_id) }}" method="POST">
                    @csrf
                    <!-- / Billing address-->
                        <div class="card">
                            <div class="billing-address">
                                <h4 class="mb-28">{{ lang('Billing address', 'checkout') }}</h4>
                                <div class="row row-cols-1 row-cols-sm-2 g-3 mb-3">
                                    <div class="col">
                                        <label class="form-label">{{ lang('First Name', 'forms') }} : </label>
                                        <input type="firstname" class="form-control form-control-md"
                                               placeholder="{{ lang('First Name', 'forms') }}" value="{{ $user->firstname }}"
                                               readonly>
                                    </div>
                                    <div class="col">
                                        <label class="form-label">{{ lang('Last Name', 'forms') }} : </label>
                                        <input type="lastname" class="form-control form-control-md"
                                               placeholder="{{ lang('Last Name', 'forms') }}" value="{{ $user->lastname }}" readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ lang('Address', 'forms') }} : <span
                                            class="required">*</span></label>
                                    <input type="text" name="address" class="form-control form-control-md"
                                           value="{{ @$user->address->address }}" required>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ lang('City', 'forms') }} : <span
                                                    class="required">*</span></label>
                                            <input type="text" name="city" class="form-control form-control-md"
                                                   value="{{ @$user->address->city }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ lang('State', 'forms') }} : <span
                                                    class="required">*</span></label>
                                            <input type="text" name="state" class="form-control form-control-md"
                                                   value="{{ @$user->address->state }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ lang('Postal code', 'forms') }} : <span
                                                    class="required">*</span></label>
                                            <input type="text" name="zip" class="form-control form-control-md"
                                                   value="{{ @$user->address->zip }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">{{ lang('Country', 'forms') }} : <span
                                            class="required">*</span></label>
                                    <select name="country" class="form-select form-select-md" required>
                                        @foreach (countries() as $country)
                                            <option value="{{ $country->id }}"
                                                {{ $country->name == @$user->address->country ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- / Billing address-->
                        <!-- / Payment Methods-->
                        <div class="card">
                            <div class="payment-method">
                                <h4 class="mb-28">{{ lang('Payment Methods', 'checkout') }}</h4>
                                @if ($transaction->total != 0)
                                    @forelse ($paymentGateways as $paymentGateway)
                                        <div class="method-type mb-16">
                                            <div data-bs-toggle="collapse" data-bs-target="#{{ $paymentGateway->key }}" aria-expanded="false" aria-controls="{{ $paymentGateway->key }}" class="d-flex flex-wrap">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="payment_method"
                                                           id="{{ $paymentGateway->key }}" value="{{ $paymentGateway->id }}"
                                                        {{ $loop->first ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $paymentGateway->key }}">{{ $paymentGateway->name }}</label>
                                                </div>
                                                <img class="h-30-px ml-auto"  src="{{ asset('storage/payments/'.$paymentGateway->logo) }}" alt="{{ $paymentGateway->name }}">
                                            </div>
                                            <div class="collapse" id="{{ $paymentGateway->key }}">
                                                <div class="card-inner-wrapper mt-16">
                                                    {{ lang('You will be redirected to the payment page for complete payment.', 'checkout') }}
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="alert alert-info mb-0">
                                            {{ lang('No payment methods available right now please try again later.', 'checkout') }}
                                        </div>
                                    @endforelse
                                @else
                                    <div class="alert alert-info mb-0">
                                        {{ lang('No payment method needed.', 'checkout') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- / Payment Methods-->

                        @if ($transaction->total != 0)
                            <button form="checkoutForm" class="button -primary -lg transform-none px-20 push-right mb-40 mt-15">{{ lang('Pay Now', 'checkout') }} <i class="fa-regular fa-arrow-right-long ml-5 push-this"></i></button>
                        @else
                            <button form="checkoutForm" class="button -primary -lg transform-none px-20 push-right mb-40 mt-15">{{ lang('Continue', 'checkout') }} <i class="fa-regular fa-arrow-right-long ml-5 push-this"></i></button>
                        @endif
                    </form>
                </div>
                <!-- / Order Box-->
                <div class="col-12 col-md-5 col-lg-4">
                    <div class="card">
                        <div class="order-summary-widget">
                            <h4 class="mb-28">{{ lang('Order Summary', 'checkout') }}</h4>
                            <div class="d-flex justify-content-between align-items-center mb-16">
                                <span class="text-dark">
                                    {{ $transaction->plan->name . ' ' . lang('Plan', 'checkout') }}
                                    ({{ format_interval($transaction->plan->interval) }})
                                </span>
                                <span class="h6 mb-0">
                                    {{ price_symbol_format($transaction->details_before_discount->price) }}
                                </span>
                            </div>
                            @if ($transaction->coupon)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-success">
                                            {{ lang('Discount', 'checkout') }} ({{ $transaction->coupon->percentage }}%)
                                        </span>
                                    <span class="h6 mb-0">
                                            -{{ price_symbol_format($transaction->details_before_discount->price - $transaction->price) }}
                                        </span>
                                </div>
                                <div class="total d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-dark">{{ lang('Subtotal', 'checkout') }}</span>
                                    <span class="h6 mb-0">{{ price_symbol_format($transaction->details_after_discount->price) }}</span>
                                </div>
                            @endif
                            @if ($transaction->details_before_discount->tax != 0)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-dark">{{ lang('Tax', 'checkout') }}</span>
                                    <span class="h6 mb-0">
                                        @if ($transaction->coupon)
                                            +{{ price_symbol_format($transaction->details_after_discount->tax) }}
                                        @else
                                            +{{ price_symbol_format($transaction->details_before_discount->tax) }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                            <div class="separator-1px-op-l mb-16"></div>
                            <div class="total d-flex justify-content-between align-items-center h6 mb-16 text-primary">
                                <span class="mb-0 h5">{{ lang('Total', 'checkout') }}</span>
                                <span class="mb-0 h5">{{ price_code_format($transaction->total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Order Box-->
            </div>
        </div>
    </section>
@endsection
