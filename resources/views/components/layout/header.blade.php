<header class="app-header">
    <div class="app-header__inner">
        <div class="app-header__column">
            <a class="app-header__logo" href="{{ URL::route('root.dashboard') }}" aria-label="{{ Config::get('app.name') }}">
                <img src="{{ URL::asset('vendor/root/img/root-logo.svg') }}" alt="">
            </a>
            <div class="app-header__actions app-header__actions--secondary">
                <button
                    type="button"
                    class="btn btn--outline-dark btn--sm btn--icon display--none:md"
                    aria-label="Open navigation"
                    x-bind:aria-expanded="sidebarOpen"
                    x-on:click="sidebarOpen = ! sidebarOpen"
                >
                    <x-root::icon name="menu-open" class="btn__icon" />
                </button>
                {{-- <button class="btn btn--outline-dark btn--sm btn--icon display--none:md" aria-label="Open search">
                    <x-root::icon name="search" class="btn__icon" />
                </button> --}}
                <x-root::layout.theme />
            </div>
            {{-- <x-root::layout.breadcrumbs /> --}}
        </div>
        <div class="app-header__actions">
            <x-root::layout.notifications />
            <div class="user-menu" x-data="{ open: false }" x-on:click.outside="open = false">
                <img class="user-menu__avatar" src="{{ $user->avatar }}" alt="{{ $user->name }}">
                <div class="user-menu__caption">
                    <span class="user-menu__role">{{ $user->name }}</span>
                    <span class="user-menu__display-name">{{ $user->email }}</span>
                </div>
                <button type="button" class="user-menu__toggle" x-bind:aria-expanded="open" x-on:click="open = ! open">
                    <x-root::icon name="chevron-down" class="open-search__icon" />
                </button>
                <ul class="context-menu" x-bind:class="{ 'is-open': open }">
                    <li>
                        <button type="submit" form="logout-form" class="context-menu__item">
                            {{ __('Logout') }}
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
