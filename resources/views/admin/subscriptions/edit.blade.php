<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{admin_lang('Edit Subscription')}}</h2>
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
        @if ($subscription->isCancelled())
            <div class="alert bg-danger text-white">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>{{ admin_lang('This subscription has been canceled') }}</strong>
            </div>
        @endif
        <form action="{{ route('admin.subscriptions.update', $subscription->id) }}" method="post" enctype="multipart/form-data" id="sidePanel_form">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Status') }} *</label>
                <select name="status" class="form-control">
                    <option value="1" {{ $subscription->status == 1 ? 'selected' : '' }}>
                        {{ admin_lang('Active') }}
                    </option>
                    <option value="0" {{ $subscription->status == 0 ? 'selected' : '' }}>
                        {{ admin_lang('Canceled') }}
                    </option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Plan') }} *</label>
                <select id="subscriptionPlan" name="plan" class="form-control" required>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}"
                            {{ $subscription->plan->id == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }}
                            {{ $plan->interval == 1 ? admin_lang('(Monthly)') : admin_lang('(Yearly)') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ admin_lang('Expiry at') }} *</label>
                <input type="datetime-local" name="expiry_at" class="form-control"
                       value="{{ \Carbon\Carbon::parse($subscription->expiry_at)->format('Y-m-d\TH:i:s') }}" required>
            </div>

        </form>
    </div>
</div>
