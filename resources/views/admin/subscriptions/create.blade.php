<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{lang('Add Subscriptions')}}</h2>
            </div>
            <div class="slidePanel-actions">
                <button id="post_sidePanel_data" class="btn btn-icon btn-primary" title="{{lang('Save')}}">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn btn-icon btn-default slidePanel-close" title="{{lang('Close')}}">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner" id="addModal">
        <form action="{{ route('admin.subscriptions.store') }}" method="post" id="sidePanel_form">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ lang('User') }} *</label>
                <select name="user" class="form-control quick-select2" required>
                    <option value="" selected disabled>{{ lang('Choose') }}</option>
                    @foreach ($users as $user)
                        @if (!$user->isSubscribed())
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">{{ lang('Plan') }} *</label>
                <select name="plan" class="form-control" required>
                    <option value="" selected disabled>{{ lang('Choose') }}</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}">
                            {{ $plan->name }}
                            {{ $plan->interval == 1 ? lang('(Monthly)') : lang('(Yearly)') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>
<script>
    $('.quick-select2').select2();
</script>
