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
                <button class="btn btn--outline-dark btn--sm btn--icon display--none:md" aria-label="Open search">
                    <x-root::icon name="search" class="btn__icon" />
                </button>
                <div class="theme-switcher" id="theme-switcher" data-theme-mode="light">
                    <button
                        type="button"
                        class="btn btn--outline-dark btn--sm btn--icon theme-switcher__system-mode"
                        aria-label="Switch to light mode"
                        data-action="light"
                    >
                        <x-root::icon name="light-mode" class="btn__icon" />
                    </button>
                    <button
                        type="button"
                        class="btn btn--outline-dark btn--sm btn--icon theme-switcher__light-mode"
                        aria-label="Switch to dark mode"
                        data-action="dark"
                    >
                        <x-root::icon name="system-mode" class="btn__icon" />
                    </button>
                    <button
                        type="button"
                        class="btn btn--outline-dark btn--sm btn--icon theme-switcher__dark-mode"
                        aria-label="Switch to system mode"
                        data-action="system"
                    >
                        <x-root::icon name="system-mode" class="btn__icon" />
                    </button>
                </div>
            </div>
            <x-root::layout.breadcrumbs />
        </div>
        <div class="app-header__actions" x-data="{ open: false }" x-on:click.outside="open = false">
            <div class="user-menu">
                <img class="user-menu__avatar" src="{{ $user->avatar }}" alt="{{ $user->name }}">
                <div class="user-menu__caption">
                    <span class="user-menu__role">{{ $user->name }}</span>
                    <span class="user-menu__display-name">{{ $user->email }}</span>
                </div>
                <button type="button" class="user-menu__toggle" x-bind:aria-expanded="open" x-on:click="open = ! open">
                    <x-root::icon name="chevron-down" class="open-search__icon" />
                </button>
                <ul class="context-menu" x-bind:data-state="open ? 'open' : 'closed'">
                    <li>
                        <button type="submit" form="logout-form">
                            {{ __('Logout') }}
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
