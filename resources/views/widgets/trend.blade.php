<turbo-frame id="widget-{{ $key }}">
    <div {{ $attrs }}>
        <div class="app-widget__column">
            <h2 class="app-widget__title">
                {{ $name }}
                <div class="form-group">
                    <label class="form-label sr-only" for="widget-{{ $key }}-interval">{{ __('Interval') }}</label>
                    <select class="form-control form-control--sm" id="widget-{{ $key }}-interval">
                        <option value="this-week">This Week</option>
                        <option value="last-week">Last Week</option>
                        <option value="last-month">Last Month</option>
                    </select>
                </div>
            </h2>
            <p class="app-widget__data">65</p>
            <div class="trending trending--up app-widget__trending">
                <span class="trending__caption">+12%</span>
                <x-root::icon name="trending-up" class="trending__icon" />
            </div>
        </div>
        <div class="app-widget__chart">
            <div id="chart01"></div>
        </div>
    </div>
</turbo-frame>
