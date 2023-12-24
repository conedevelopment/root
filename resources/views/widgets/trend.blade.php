<turbo-frame id="widget-{{ $key }}">
    <div {{ $attrs }}>
        <div class="app-widget__column">
            <h2 class="app-widget__title">
                {{ $name }}
            </h2>
            <p class="app-widget__data">65</p>
            <div class="trending trending--up app-widget__trending">
                <span class="trending__caption">+12%</span>
                <x-root::icon name="trending-up" class="trending__icon" />
            </div>
        </div>
        <div class="app-widget__chart">
            <div x-init=""></div>
        </div>
    </div>
</turbo-frame>
