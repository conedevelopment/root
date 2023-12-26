<turbo-frame id="widget-{{ $key }}" @if(! $isTurbo) src="{{ $url }}" @endif>
    <div {{ $attrs }}>
        <div class="app-widget__column">
            <h2 class="app-widget__title">
                {{ $name }}
            </h2>
            @if(! is_null($data['value']))
                <p class="app-widget__data">{{ $value }}</p>
            @endif
        </div>
        <div class="app-widget__chart">
            <x-root::chart :config="$data['chart']" />
        </div>
    </div>
</turbo-frame>
