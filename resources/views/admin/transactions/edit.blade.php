<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ admin_lang('Transaction Details') }}</h2>
            </div>
            <div class="slidePanel-actions">
                <button class="btn btn-default btn-icon slidePanel-close" title="{{ admin_lang('Close') }}">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        @if ($transaction->isCancelled())
            <div class="alert alert-danger">
                <p class="mb-0">{{ admin_lang('Transaction has been canceled') }}</p>
            </div>
        @endif
        <div class="card mb-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <span>{{ admin_lang('Plan Price') }}</span>
                    <strong>{{ price_symbol_format($transaction->details_before_discount->price) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <span>{{ admin_lang('Taxes') }}</span>
                    <strong>+{{ price_symbol_format($transaction->details_before_discount->tax) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <span>{{ admin_lang('Subtotal') }}</span>
                    <strong>{{ price_symbol_format($transaction->details_before_discount->total) }}</strong>
                </li>
            </ul>
        </div>

        <div class="card mb-3">
            <ul class="list-group list-group-flush">
                @if ($transaction->details_after_discount)
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                <span>{{ admin_lang('Discount') }}
                                    @if ($transaction->coupon_id)
                                        (<a
                                            href="{{ route('admin.coupons.edit', $transaction->coupon_id) }}">{{ admin_lang('View Coupon') }}</a>)
                                    @endif
                                </span>
                        <span
                            class="text-danger"><strong>-{{ price_symbol_format($transaction->details_before_discount->total - $transaction->details_after_discount->total) }}</strong></span>
                    </li>
                @endif
                @if ($transaction->gateway)
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <span>{{ admin_lang('Gateway Fees') }}</span>
                        <strong>+{{ price_symbol_format($transaction->fees) }}</strong>
                    </li>
                @endif
                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <strong class="text-dark">{{ admin_lang('Total') }}</strong>
                    <strong class="text-dark">{{ price_symbol_format($transaction->total) }}</strong>
                </li>
            </ul>
        </div>
        @if ($transaction->isPaid())
            <form action="{{ route('admin.transactions.update', $transaction->id) }}" method="POST" onsubmit='return confirm("{{admin_lang('Are you sure?')}}")'>
                @csrf
                @method('PUT')
                <button class="btn btn-label-danger btn-lg w-100">
                    <i class="far fa-times me-2"></i>
                    <span>{{ admin_lang('Cancel Transaction') }}</span>
                </button>
            </form>
        @endif
    </div>
</div>

