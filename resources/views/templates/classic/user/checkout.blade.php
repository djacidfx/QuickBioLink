@extends($activeTheme.'layouts.main')
@section('title', lang('Checkout'))
@section('content')
    <section class="page-banner-area theme-gradient-3 pt-170">
        <div class="container">
            <div class="row wow fadeInUp" data-wow-delay="300ms">
                <div class="col-md-10 col-xl-8 mx-auto">
                    <div class="d-flex flex-column align-items-center">
                        <h2>{{ lang('Checkout')  }}</h2>
                        <p>{{ lang('Experience the joy of hassle-free payments.')  }}</p>
                        <ol class="breadcrumb text-grey-2">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ lang('Home') }}</a></li>
                            <li class="breadcrumb-item active text-dark-1" aria-current="page">{{ lang('Checkout')  }}</li>
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
                                    {{ lang('Important Notice !') }}
                                </h5>
                                <p class="fst-italic font-15 text-dark-2">{{ lang('downgrading notice') }}</p>
                            </blockquote>
                        </div>
                    @elseif($transaction->type == 3)
                        <div class="blockquote-style1 mb-60">
                            <blockquote class="blockquote">
                                <h5 class="quote-title"><i class="fa-regular fa-circle-question me-2"></i>
                                    {{ lang('Important Notice !') }}</h5>
                                <p class="fst-italic font-15 text-dark-2">{{ lang('upgrading notice') }}</p>
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
                                                <span><i class="fa fa-window-maximize"></i> {{ lang('Have a coupon?') }}</span>
                                                <button class="accordion-toggle collapsed text-decoration -underline-2 ml-8"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                        aria-expanded="false" aria-controls="collapseOne">{{ lang('Click here to enter your code') }}
                                                </button>
                                            </div>
                                        </div>
                                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                             data-bs-parent="#accordion">
                                            <div class="card-body border-top pt-20 pb-30">
                                                <p>{{ lang('If you have a coupon code, please apply it below.') }}</p>
                                                <div class="coupon-code-input w-50">
                                                    <form action="{{ route('checkout.coupon.apply', $transaction->checkout_id) }}"
                                                          method="POST">
                                                        @csrf
                                                        <div class="input-group">
                                                            <input type="text" name="coupon_code" class="form-control text-field"
                                                                   placeholder="{{ lang('Enter coupon code') }}" max="20" value="{{ old('coupon_code') }}" required>
                                                            <button class="button -primary transform-none h-48-px">{{ lang('Apply') }}</button>
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
                                    <i class="fa-solid fa-ticket me-2"></i> {{ lang('Coupon code') }} <span class="fw-bold">{{ $transaction->coupon->code }}</span> {{ lang('Applied') }}.
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


                <h4 class="my-30">{{ lang('Checkout') }}</h4>

                <div class="col-12 col-md-7 col-lg-8">
                    <form id="checkoutForm" action="{{ route('checkout.process', $transaction->checkout_id) }}" method="POST">
                    @csrf
                    <!-- / Billing address-->
                        <div class="card">
                            <div class="billing-address">
                                <h4 class="mb-28">{{ lang('Billing address') }}</h4>
                                <div class="row row-cols-1 row-cols-sm-2 g-3 mb-3">
                                    <div class="col">
                                        <label class="form-label">{{ lang('First Name') }} : </label>
                                        <input type="firstname" class="form-control form-control-md"
                                               placeholder="{{ lang('First Name') }}" value="{{ $user->firstname }}"
                                               readonly>
                                    </div>
                                    <div class="col">
                                        <label class="form-label">{{ lang('Last Name') }} : </label>
                                        <input type="lastname" class="form-control form-control-md"
                                               placeholder="{{ lang('Last Name') }}" value="{{ $user->lastname }}" readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ lang('Address') }} : <span
                                            class="required">*</span></label>
                                    <input type="text" name="address" class="form-control form-control-md"
                                           value="{{ @$user->address->address }}" required>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ lang('City') }} : <span
                                                    class="required">*</span></label>
                                            <input type="text" name="city" class="form-control form-control-md"
                                                   value="{{ @$user->address->city }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ lang('State') }} : <span
                                                    class="required">*</span></label>
                                            <input type="text" name="state" class="form-control form-control-md"
                                                   value="{{ @$user->address->state }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ lang('Postal code') }} : <span
                                                    class="required">*</span></label>
                                            <input type="text" name="zip" class="form-control form-control-md"
                                                   value="{{ @$user->address->zip }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">{{ lang('Country') }} : <span
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
                    @if ($transaction->total != 0)
                        <!-- / Payment Methods-->
                        <div class="card">
                            <div class="payment-method">
                                <h4 class="mb-28">{{ lang('Payment Methods') }}</h4>

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
                                                    {{ lang('You will be redirected to the payment page for complete payment.') }}
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="alert alert-info mb-0">
                                            {{ lang('No payment methods available right now please try again later.') }}
                                        </div>
                                    @endforelse

                            </div>
                        </div>
                        <!-- / Payment Methods-->
                        @endif

                        @if ($transaction->total != 0)
                            <button form="checkoutForm" class="button -primary -lg transform-none px-20 push-right mb-40 mt-15">{{ lang('Pay Now') }} <i class="fa-regular fa-arrow-right-long ml-5 push-this"></i></button>
                        @else
                            <button form="checkoutForm" class="button -primary -lg transform-none px-20 push-right mb-40 mt-15">{{ lang('Continue') }} <i class="fa-regular fa-arrow-right-long ml-5 push-this"></i></button>
                        @endif
                    </form>
                </div>
                <!-- / Order Box-->
                <div class="col-12 col-md-5 col-lg-4">
                    <div class="card">
                        <div class="order-summary-widget">
                            <h4 class="mb-28">{{ lang('Order Summary') }}</h4>
                            <div class="d-flex justify-content-between align-items-center mb-16">
                                <span class="text-dark">
                                    {{ $transaction->plan->name . ' ' . lang('Plan') }}
                                    ({{ format_interval($transaction->plan->interval) }})
                                </span>
                                <span class="h6 mb-0">
                                    {{ price_symbol_format($transaction->details_before_discount->price) }}
                                </span>
                            </div>
                            @if ($transaction->coupon)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-success">
                                            {{ lang('Discount') }} ({{ $transaction->coupon->percentage }}%)
                                        </span>
                                    <span class="h6 mb-0">
                                            -{{ price_symbol_format($transaction->details_before_discount->price - $transaction->price) }}
                                        </span>
                                </div>
                                <div class="total d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-dark">{{ lang('Subtotal') }}</span>
                                    <span class="h6 mb-0">{{ price_symbol_format($transaction->details_after_discount->price) }}</span>
                                </div>
                            @endif
                            @if ($transaction->details_before_discount->tax != 0)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-dark">{{ lang('Tax') }}</span>
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
                                <span class="mb-0 h5">{{ lang('Total') }}</span>
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
