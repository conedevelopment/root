<turbo-frame id="widget-{{ $key }}" @if(! $isTurbo) src="{{ $url }}" @endif>
    <div {{ $attrs }}>
        <div class="app-widget__column">
            <h2 class="app-widget__title">
                {{ $name }}
            </h2>
            @if(! is_null($data['current']))
                <p class="app-widget__data">{{ $data['current'] }}</p>
            @endif
        </div>
        <div class="app-widget__chart">
            <form method="GET" action="{{ $url }}" onchange="this.requestSubmit();">
                <div class="form-group">
                    <label class="form-label sr-only" for="widget-{{ $key }}}-interval">{{ __('Interval') }}</label>
                    <select class="form-control form-control--sm" name="range" id="widget-{{ $key }}}-interval">
                        @foreach($ranges as $range => $rangeLabel)
                            <option value="{{ $range }}" @selected($range === $currentRange)>{{ $rangeLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            <x-root::chart :config="$data['chart']" />
        </div>
    </div>
</turbo-frame>
