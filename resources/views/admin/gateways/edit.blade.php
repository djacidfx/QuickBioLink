<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ lang('Edit') .' '. $gateway->name }}</h2>
            </div>
            <div class="slidePanel-actions">
                <button id="post_sidePanel_data" class="btn btn-icon btn-primary" title="{{ lang('Save') }}">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn btn-default btn-icon slidePanel-close" title="{{ lang('Close') }}">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form action="{{ route('admin.gateways.update', $gateway->id) }}" method="post" id="sidePanel_form">
            @csrf
            <div class="d-flex align-items-start justify-content-between gap-4">
                <div>
                    <label for="upload" class="btn btn-primary mb-2" tabindex="0">
                        <i class="fas fa-upload"></i>
                        <span class="d-none d-sm-block ms-2">{{ lang('Change Logo') }}</span>
                        <input name="avatar" type="file" id="upload" hidden
                               onchange="readURL(this,'uploadedLogo')"
                               accept="image/png, image/jpeg">
                    </label>
                    <p class="form-text mb-0">{{ lang('Allowed JPG, JPEG or PNG.') }}</p>
                </div>
                <img src="{{ asset('storage/payments/'.$gateway->logo) }}" alt="logo"
                     class="d-block rounded" width="150" id="uploadedLogo">
            </div>
            <hr>
            <div class="mb-3">
                {{quick_switch(lang('Active'), 'status', $gateway->status == '1')}}
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Name') }} *</label>
                <input type="text" name="name" class="form-control" value="{{ $gateway->name }}"
                       required>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ lang('Gateway fees') }} *</label>
                <div class="input-group">
                    <input type="number" name="gateway_fees" class="form-control" placeholder="0"
                           value="{{ $gateway->fees }}">
                    <span class="input-group-text"><i class="icon-feather-percent"></i></span>
                </div>
            </div>
            @if (!is_null($gateway->test_mode))
                <div class="mb-3">
                    {{quick_switch(lang('Test Mode'), 'test_mode', $gateway->test_mode == '1')}}
                </div>
            @endif
            @foreach ($gateway->credentials as $key => $value)
                <div class="mb-3">
                    <label class="form-label text-capitalize">{{ $gateway->name }}
                        {{ str_replace('_', ' ', $key) }}</label>
                    <input type="text" name="credentials[{{ $key }}]"
                           value="{{ demo_mode() ? '' : $value }}" class="form-control">
                </div>
            @endforeach

            @switch($gateway->key)
                @case('paypal')
                <p>{{lang('Get the API details from')}} <a href="https://developer.paypal.com/developer/applications/create" target="_blank">{{lang('here')}} <i class="far fa-external-link"></i></a></p>
                @break

                @case('stripe')
                <p>{{lang('Get the API details from')}} <a href="https://dashboard.stripe.com/apikeys" target="_blank">{{lang('here')}} <i class="far fa-external-link"></i></a></p>
                @break

                @case('mollie')
                <p>{{lang('Get the API details from')}} <a href="https://www.mollie.com/dashboard" target="_blank">{{lang('here')}} <i class="far fa-external-link"></i></a></p>
                @break

                @case('razorpay')
                <p>{{lang('Get the API details from')}} <a href="https://dashboard.razorpay.com/app/keys" target="_blank">{{lang('here')}} <i class="far fa-external-link"></i></a></p>
                @break
            @endswitch
        </form>
    </div>
</div>
