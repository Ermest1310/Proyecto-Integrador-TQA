@props(['title', 'value', 'description', 'icon', 'trend' => null, 'type' => 'default'])

@php
    $borderColor = match($type) {
        'gold' => 'var(--alpha-gold)',
        'dark' => 'var(--alpha-navy-dark)',
        'danger' => 'var(--alpha-danger)',
        default => 'var(--alpha-blue-light)',
    };
@endphp

<div {{ $attributes->merge(['class' => 'alpha-kpi-card']) }} style="border-left: 4px solid {{ $borderColor }};">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <span class="alpha-kpi-title">{{ $title }}</span>
            <h3 class="alpha-kpi-value my-1">{{ $value }}</h3>
            <span class="alpha-kpi-desc">{{ $description }}</span>
        </div>
        <div class="alpha-kpi-icon-wrapper" style="background-color: {{ $borderColor }}10; color: {{ $borderColor }};">
            <i class="{{ $icon }} fa-xl"></i>
        </div>
    </div>
    @if($trend)
        <div class="alpha-kpi-trend mt-2">
            <span class="{{ $trend > 0 ? 'text-success' : 'text-muted' }} small fw-semibold">
                <i class="fas {{ $trend > 0 ? 'fa-arrow-up' : 'fa-minus' }} me-1"></i> 
                {{ $trend }} en las últimas 24h
            </span>
        </div>
    @endif
</div>

<style>
.alpha-kpi-card {
    background: var(--alpha-card-bg);
    border-radius: var(--alpha-radius-md);
    padding: 24px;
    box-shadow: var(--alpha-shadow-sm);
    transition: var(--alpha-transition);
    position: relative;
    overflow: hidden;
}
.alpha-kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--alpha-shadow-lg);
}
.alpha-kpi-title {
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    font-weight: 700;
    color: var(--alpha-text-muted);
}
.alpha-kpi-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--alpha-navy-dark);
    letter-spacing: -0.5px;
}
.alpha-kpi-desc {
    font-size: 0.75rem;
    color: var(--alpha-text-muted);
}
.alpha-kpi-icon-wrapper {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    transition: var(--alpha-transition);
}
.alpha-kpi-card:hover .alpha-kpi-icon-wrapper {
    transform: scale(1.1);
}
</style>