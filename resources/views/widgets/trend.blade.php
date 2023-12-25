<turbo-frame id="widget-{{ $key }}">
    <div {{ $attrs }}>
        <div class="app-widget__column">
            <h2 class="app-widget__title">
                {{ $name }}
            </h2>
            <p class="app-widget__data">65</p>
        </div>
        <div class="app-widget__chart">
            <div x-data="trend({{ json_encode($config) }})"></div>
        </div>
    </div>
</turbo-frame>
