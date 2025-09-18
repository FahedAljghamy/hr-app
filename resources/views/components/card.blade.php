<div class="card shadow {{ $class }}">
    @if($title || $icon)
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-{{ $color }}">
            @if($icon)
                <i class="{{ $icon }}"></i>
            @endif
            {{ $title }}
        </h6>
        {{ $header ?? '' }}
    </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
</div>