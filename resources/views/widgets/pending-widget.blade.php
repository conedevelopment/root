<turbo-frame id="widget-{{ $widget['key'] }}" src="{{ $widget['url'] }}">
    <div {{ $attrs }}>
        <div class="app-widget__column">
            <h2 class="app-widget__title">{{ $name }}</h2>
            <p class="app-widget__data">{{ __('Loading') }}...</p>
        </div>
    </div>
</turbo-frame>
