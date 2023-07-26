<div class="l-main__sidebar" x-bind:class="{ 'l-main__sidebar--open': sidebarOpen }">
    <aside class="app-sidebar">
        <div class="app-sidebar__header">
            <a class="app-sidebar__logo" href="{{ URL::route('root.dashboard') }}" aria-label="{{ Config::get('app.name') }}">
                <img src="{{ URL::asset('vendor/root/img/root-logo.svg') }}">
            </a>
            <div class="app-sidebar__search">
                <span class="open-search">
                    <x-root::icon name="search" class="open-search__icon"/>
                    <span class="open-search__caption">{{ __('Search') }}</span>
                    <button
                        type="button"
                        class="btn btn--outline-primary btn--sm open-search__btn"
                    >
                        Ctrl + K
                    </button>
                </span>
            </div>
            <button
                type="button"
                class="btn btn--outline-dark btn--sm btn--icon display--none:md"
                data-action="sidebar-close"
                aria-label="{{ __('Close navigation') }}"
                x-bind:aria-expanded="sidebarOpen"
                @click="sidebarOpen = false"
            >
                <x-root::icon name="close" class='btn__icon'/>
            </button>
        </div>
        <div class="app-sidebar__body">
            <div class="block-navigation">
                <nav class="block-navigation__menu block-navigation__menu--breakout" data-state="open">
                    <ul>
                        <li>
                            <a
                                href="{{ URL::route('root.dashboard') }}"
                                aria-current="{{ Route::currentRouteName() === 'root.dashboard' ? 'page' : '' }}"
                            >
                                <x-root::icon name="home" />
                                {{ __('Dashboard') }}
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            @foreach($groups as $group => $items)
                <div class="block-navigation" x-data="{ open: true }">
                    <h3 class="block-navigation__title">
                        {{ $group }}
                        <button
                            type="button"
                            class="btn btn--light btn--sm btn--icon block-navigation__toggle"
                            aria-label="{{ __('Toggle navigation') }}"
                            x-bind:aria-expanded="open"
                            @click="open = ! open"
                        >
                            <x-root::icon name="chevron-down" class='btn__icon'/>
                        </button>
                    </h3>
                    <nav class="block-navigation__menu block-navigation__menu--breakout" x-bind:data-state="open ? 'open' : 'closed'">
                        <ul>
                            @foreach($items as $item)
                                <li>
                                    <a href="{{ $item->url }}" aria-current="{{ $item->matched() ? 'page' : '' }}">
                                        @if($item->icon)
                                            <x-root::icon :name="$item->icon" class='icon'/>
                                        @endif
                                        {{ $item->label }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </div>
            @endforeach
        </div>
    </aside>
</div>
