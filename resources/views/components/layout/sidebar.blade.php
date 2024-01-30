<div class="l-main__sidebar" x-bind:class="{ 'l-main__sidebar--open': sidebarOpen }">
    <aside class="app-sidebar">
        <div class="app-sidebar__header">
            <a class="app-sidebar__logo" href="{{ URL::route('root.dashboard') }}" aria-label="{{ Config::get('app.name') }}">
                <img src="{{ URL::asset('vendor/root/img/root-logo.svg') }}">
            </a>
            <button
                type="button"
                class="btn btn--outline-dark btn--sm btn--icon display--none:md"
                data-action="sidebar-close"
                aria-label="{{ __('Close navigation') }}"
                x-bind:aria-expanded="sidebarOpen"
                x-on:click="sidebarOpen = false"
            >
                <x-root::icon name="close" class='btn__icon'/>
            </button>
        </div>
        <div class="app-sidebar__body">
            <div class="block-navigation">
                <nav class="block-navigation__menu block-navigation__menu--breakout is-open">
                    <ul>
                        <li>
                            <a
                                href="{{ URL::route('root.dashboard') }}"
                                @if(Route::currentRouteName() === 'root.dashboard') aria-current="page" @endif
                            >
                                <x-root::icon name="home" />
                                {{ __('Dashboard') }}
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            @foreach($groups as $group => $items)
                <x-root::layout.sidebar-group :title="$group" :items="$items" />
            @endforeach
        </div>
    </aside>
</div>
