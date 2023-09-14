<form {{ $attrs }}>
    @if($search)
        {!! $search !!}
    @endif
    @if(! empty($fields))
        <div class="data-table-filter" x-data="{ open: false }" x-on:click.outside="open = false">
            <button
                type="button"
                class="btn btn--light btn--icon btn--counter data-table-filter__toggle"
                x-bind:aria-expanded="open"
                x-on:click="open = ! open"
            >
                <x-root::icon name="filter" class="btn__icon" />
                @if($attrs->get('data-active'))
                    <span class="btn__counter">{{ $attrs->get('data-active') }}</span>
                @endif
            </button>
            <div class="context-menu context-menu--inline-end" x-bind:data-state="open ? 'open' : 'closed'">
                <div class="form-group-stack form-group-stack--bordered form-group-container">
                    @foreach($fields as $field)
                        {!! $field !!}
                    @endforeach
                    <div class="data-table-filter__actions">
                        <button type="submit" class="btn btn--primary btn--sm">
                            {{ __('Filter') }}
                        </button>
                        <button type="reset" class="btn btn--light btn--sm">
                            {{ __('Reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</form>
