<div class="btn-dropdown" x-data="{ open: false }" x-on:click.outside="open = false">
    <button
        @class(['btn', 'btn--outline-primary', 'btn--icon', $class ?? null])
        x-bind:aria-expanded="open"
        x-on:click="open = ! open"
    >
        {{ __('Actions') }}
        <x-root::icon name="chevron-down" class="btn__icon" />
    </button>
    <ul x-cloak x-show="open" x-transition class="context-menu context-menu--inline-end">
        @foreach($actions as $action)
            <li>
                <button
                    type="button"
                    class="context-menu__item"
                    x-on:click="$dispatch('open-{{ $action['modalKey'] }}')"
                >
                    {{ $action['name'] }}
                </button>
                @include($action['template'], $action)
            </li>
        @endforeach
    </ul>
</div>
