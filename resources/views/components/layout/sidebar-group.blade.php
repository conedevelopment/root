<div class="block-navigation" x-data="{ open: true }">
    <h3 class="block-navigation__title">
        {{ $title }}
        <button
            type="button"
            class="btn btn--light btn--sm btn--icon block-navigation__toggle"
            aria-label="{{ __('Toggle navigation') }}"
            x-bind:aria-expanded="open"
            x-on:click="open = ! open"
        >
            <x-root::icon name="chevron-down" class="btn__icon" />
        </button>
    </h3>
    <nav class="block-navigation__menu block-navigation__menu--breakout" x-bind:data-state="open ? 'open' : 'closed'">
        <ul>
            @foreach($items as $item)
                <li>
                    <a href="{{ $item->url }}" @if($item->partiallyMatched()) aria-current="page" @endif>
                        @if($item->icon)
                            <x-root::icon :name="$item->icon" class="icon" />
                        @endif
                        {{ $item->label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
