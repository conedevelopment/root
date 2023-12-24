<turbo-frame id="widget-{{ $key }}">
    <div {{ $attrs }}>
        @if(! is_null($icon))
            <div class="app-widget__icon">
                <x-root::icon :name="$icon" />
            </div>
        @endif
        <div class="app-widget__column">
            <h2 class="app-widget__title">
                {{ $name }}
            </h2>
            <p class="app-widget__data">{{ $data['current'] }}</p>
            @if($data['trend'] < 0)
                <div class="trending trending--down app-widget__trending">
                    <span class="trending__caption">{{ $data['trend'] }}%</span>
                    <x-root::icon name="trending-down" class="trending__icon" />
                </div>
            @elseif($data['trend'] > 0)
                <div class="trending trending--up app-widget__trending">
                    <span class="trending__caption">+{{ $data['trend'] }}%</span>
                    <x-root::icon name="trending-up" class="trending__icon" />
                </div>
            @endif
        </div>
    </div>
</turbo-frame>
