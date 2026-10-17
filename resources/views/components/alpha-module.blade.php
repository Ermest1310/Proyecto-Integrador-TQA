@props(['title', 'route', 'icon', 'description'])

<a href="{{ $route }}" class="text-decoration-none col-xl-4 col-md-6 mb-4 animate-up">
    <div class="alpha-module-card h-100">
        <div class="module-glow"></div>
        <div class="card-body-content p-4">
            <div class="d-flex align-items-center mb-3">
                <div class="module-icon-box">
                    <i class="{{ $icon }}"></i>
                </div>
                <h5 class="module-title mb-0 ms-3">{{ $title }}</h5>
            </div>
            <p class="module-text mb-0">{{ $description }}</p>
            <div class="module-footer-arrow mt-4 d-flex align-items-center text-uppercase font-monospace">
                <span>Ingresar Módulo</span>
                <i class="fas fa-chevron-right ms-2 transition-arrow"></i>
            </div>
        </div>
    </div>
</a>

<style>
.alpha-module-card {
    background: var(--alpha-card-bg);
    border: 1px solid rgba(11, 25, 44, 0.05);
    border-radius: var(--alpha-radius-lg);
    position: relative;
    overflow: hidden;
    box-shadow: var(--alpha-shadow-sm);
    transition: var(--alpha-transition);
}
.module-glow {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 4px;
    background: linear-gradient(90deg, var(--alpha-blue-light), var(--alpha-gold));
    transition: var(--alpha-transition);
}
.alpha-module-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--alpha-shadow-lg);
    border-color: rgba(191, 161, 95, 0.25);
}
.alpha-module-card:hover .module-glow {
    height: 6px;
}
.module-icon-box {
    width: 44px;
    height: 44px;
    background-color: var(--alpha-navy-dark);
    color: var(--alpha-gold);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    font-size: 1.25rem;
    transition: var(--alpha-transition);
}
.alpha-module-card:hover .module-icon-box {
    background-color: var(--alpha-gold);
    color: white;
}
.module-title {
    font-weight: 700;
    color: var(--alpha-navy-dark);
}
.module-text {
    font-size: 0.85rem;
    color: var(--alpha-text-muted);
    line-height: 1.5;
}
.module-footer-arrow {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--alpha-blue-light);
    letter-spacing: 0.5px;
}
.alpha-module-card:hover .transition-arrow {
    transform: translateX(5px);
    color: var(--alpha-gold);
}
</style>