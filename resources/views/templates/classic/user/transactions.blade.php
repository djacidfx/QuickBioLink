@extends($activeTheme.'layouts.app')
@section('title', lang('Transactions'))
@section('content')
    <div class="d-flex justify-content-between align-items-center pb-30">
        <div class="title-head">
            <h1 class="mb-0">{{ lang('Transactions') }}</h1>
        </div>
    </div>

        <div class="card">
            <div class="card-header mb-16">
                <h2 class="card-title">{{ lang('Transactions') }}</h2>
            </div>
            <div class="card-body">
                <div class="dash-table">
                    <div class="table-responsive">
                    <table class="table table-striped table-light mb-0">
                        <thead>
                        <tr>
                            <th>{{ lang('ID') }}</th>
                            <th class="text-center">{{ lang('Price') }}</th>
                            <th class="text-center"><span
                                    class="d-none d-lg-inline">{{ lang('Payment Method') }}</span>
                            </th>
                            <th class="text-center">{{ lang('Date') }}</th>
                            <th class="text-center">{{ lang('Status') }}</th>
                            <th class="text-center">{{ lang('Invoice') }}</th>
                        </tr>
                        </thead>
                        @if ($transactions->count() > 0)
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
                                                class="badge bg-success text-light">{{ lang('Paid') }}</span>
                                        @else
                                            <span
                                                class="badge bg-primary text-light">{{ lang('Done') }}</span>
                                        @endif
                                    @else
                                        <span
                                            class="badge bg-danger">{{ lang('Cancelled') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($transaction->isPaid() && $transaction->total > 0)
                                    <a title="{{ lang('Invoice') }}" href="{{route('invoice', $transaction->id)}}" target="_blank"><i class="fas fa-paperclip"></i></a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">{{ lang('No Data Found') }}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

@endsection
