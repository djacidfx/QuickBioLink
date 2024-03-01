@extends($activeTheme.'layouts.app')
@section('title', lang('Transactions', 'account'))
@section('content')
    <div class="d-flex justify-content-between align-items-center pb-30">
        <div class="title-head">
            <h1 class="mb-0">{{ lang('Transactions', 'account') }}</h1>
        </div>
    </div>
    @if ($transactions->count() > 0)
        <div class="card">
            <div class="card-header mb-16">
                <h2 class="card-title">{{ lang('Transactions', 'account') }}</h2>
            </div>
            <div class="card-body">
                <div class="dash-table">
                    <div class="table-responsive">
                    <table class="table table-striped table-light mb-0">
                        <thead>
                        <tr>
                            <th>{{ lang('ID', 'account') }}</th>
                            <th class="text-center">{{ lang('Price', 'account') }}</th>
                            <th class="text-center"><span
                                    class="d-none d-lg-inline">{{ lang('Payment Method', 'account') }}</span>
                            </th>
                            <th class="text-center">{{ lang('Date', 'account') }}</th>
                            <th class="text-center">{{ lang('Status', 'account') }}</th>
                            <th class="text-center">{{ lang('Invoice', 'account') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>#{{ $transaction->id }}</td>
                                <td class="text-center">
                                    {{ price_symbol_format($transaction->total) }}
                                </td>
                                <td class="text-center"><span
                                        class="d-none d-lg-inline">{{ $transaction->gateway->name ?? '--' }}</span>
                                </td>
                                <td class="text-center">{{ date_formating($transaction->created_at) }}</td>
                                <td class="text-center">
                                    @if ($transaction->isPaid())
                                        @if ($transaction->total > 0)
                                            <span
                                                class="badge bg-success text-light">{{ lang('Paid', 'account') }}</span>
                                        @else
                                            <span
                                                class="badge bg-primary text-light">{{ lang('Done', 'account') }}</span>
                                        @endif
                                    @else
                                        <span
                                            class="badge bg-danger">{{ lang('Cancelled', 'account') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($transaction->isPaid() && $transaction->total > 0)
                                    <a title="{{ lang('Invoice', 'account') }}" href="{{route('invoice', $transaction->id)}}" target="_blank"><i class="fas fa-paperclip"></i></a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        @include($activeTheme.'user.empty-section')
    @endif
@endsection
