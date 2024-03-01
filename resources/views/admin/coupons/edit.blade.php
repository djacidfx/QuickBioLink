<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{admin_lang('Edit Coupon')}}</h2>
            </div>
            <div class="slidePanel-actions">
                <button id="post_sidePanel_data" class="btn btn-icon btn-primary" title="{{admin_lang('Save')}}">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn btn-icon btn-default slidePanel-close" title="{{admin_lang('Close')}}">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="post" enctype="multipart/form-data" id="sidePanel_form">
            @csrf
            @method('PUT')
            @if ($coupon->isExpiry())
                <div class="alert alert-danger">
                    {{ admin_lang('This coupon has been expired') }}
                </div>
            @endif
            <div class="mb-3">
                <label class="form-label" for="code">{{ admin_lang('Coupon code') }} *</label>
                <div class="input-group mb-2">
                    <button class="btn btn-secondary copy-btn" type="button" data-clipboard-target="#couponCodeInput" data-tippy-placement="top" title="{{ admin_lang('Copy') }}"><i class="icon-feather-copy"></i></button>
                    <input id="couponCodeInput" type="text" name="code" class="form-control"
                           placeholder="{{ admin_lang('Coupon code') }}" value="{{ $coupon->code }}" maxlength="20" required disabled>
                </div>
                <small class="form-text">{{ admin_lang('Min 3 and max 20 characters allowed') }}</small>
            </div>
            <div class="mb-3">
                <label class="form-label" for="code">{{ admin_lang('Discount percentage') }} *</label>
                <div class="custom-input-group input-group">
                    <input type="number" name="percentage" class="form-control" min="1"
                           max="100" value="{{ $coupon->percentage }}" placeholder="0" required disabled>
                    <span class="input-group-text"><i class="icon-feather-percent"></i></span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="code">{{ admin_lang('Limit for each user') }} *</label>
                <input type="number" name="limit" class="form-control" min="1"
                       placeholder="0" value="{{ $coupon->limit }}" required>
            </div>
            <h5 class="mt-4">{{ admin_lang('Usage details') }}</h5>
            <hr>
            <div class="mb-3">
                <label class="form-label" for="plan">{{ admin_lang('Plan') }} *</label>
                <select name="plan" id="plan" class="form-control" required disabled>
                    <option value="0">{{ admin_lang('All plans') }}</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" {{ $coupon->plan_id == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }}
                            ({{ format_interval($plan->interval) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="action_type">{{ admin_lang('Action') }} *</label>
                <select name="action_type" id="action_type" class="form-control" required disabled>
                    <option value="" disabled selected>{{ admin_lang('Choose') }}</option>
                    <option value="0" {{ $coupon->action_type == 0 ? 'selected' : '' }}>
                        {{ admin_lang('All actions') }}
                    </option>
                    <option value="1" {{ $coupon->action_type == 1 ? 'selected' : '' }}>
                        {{ admin_lang('Subscribing') }}
                    </option>
                    <option value="2" {{ $coupon->action_type == 2 ? 'selected' : '' }}>
                        {{ admin_lang('Renewal') }}
                    </option>
                    <option value="3" {{ $coupon->action_type == 3 ? 'selected' : '' }}>
                        {{ admin_lang('Upgrade') }}
                    </option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="expiry_at">{{ admin_lang('Expiry at') }} *</label>
                <input type="datetime-local" name="expiry_at" id="expiry_at" class="form-control"
                       value="{{ \Carbon\Carbon::parse($coupon->expiry_at)->format('Y-m-d\TH:i') }}" required>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    "use strict";
    var clipboardCopyBtn = document.querySelectorAll(".copy-btn");
    if (clipboardCopyBtn) {
        clipboardCopyBtn.forEach((el) => {
            var clipboardCopy = new ClipboardJS(clipboardCopyBtn);
            clipboardCopy.on('success', function(e) {
                toastr.success('Copied to clipboard');
            });
        });
    }

    var couponCodeInput = $('#couponCodeInput'),
        generateCouponBtn = $('#generateCouponBtn');
    $(document).on("click", "#generateCouponBtn", function (e) {
        e.preventDefault();
        couponCodeInput.val(generateCoupon(12));
    });
</script>
