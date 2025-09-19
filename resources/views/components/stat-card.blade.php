<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-{{ $color }} shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-{{ $color }} text-uppercase mb-1">
                        {{ $title }}
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $value }}</div>
                </div>
                <div class="col-auto">
                    <i class="{{ $icon }} fa-2x text-gray-300"></i>
                </div>
            </div>
            @if($percentage)
            <div class="mt-2">
                <span class="text-xs {{ $percentageColor }}">
                    <i class="fas fa-arrow-{{ $percentageColor == 'text-success' ? 'up' : 'down' }}"></i>
                    {{ $percentage }}
                </span>
                <span class="text-xs text-gray-500 ml-1">{{ trans('messages.from last month') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>