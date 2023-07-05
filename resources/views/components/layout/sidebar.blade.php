<div class="l-main__sidebar" data-item="sidebar">
    <aside class="app-sidebar">
        <div class="app-sidebar__header">
            <a class="app-sidebar__logo" href="{{ URL::route('root.dashboard') }}" aria-label="{{ Config::get('app.name') }}">
                <img src="{{ URL::asset('vendor/root/img/root-logo.svg') }}">
            </a>
            <div class="app-sidebar__search">
                <span class="open-search">
                    <svg aria-hidden='true' focusable='false' height='24' role='img' style='fill: currentColor' viewBox='0 0 24 24' width='24' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg' class='open-search__icon'>
                        <path d='M19.501,9.75c-0,2.152 -0.699,4.14 -1.875,5.752l5.935,5.94c0.585,0.586 0.585,1.537 -0,2.123c-0.586,0.586 -1.538,0.586 -2.124,0l-5.935,-5.939c-1.612,1.181 -3.6,1.875 -5.752,1.875c-5.386,-0 -9.75,-4.364 -9.75,-9.751c0,-5.386 4.364,-9.75 9.75,-9.75c5.387,-0 9.751,4.364 9.751,9.75Zm-9.751,6.751c3.704,-0 6.751,-3.047 6.751,-6.751c-0,-3.703 -3.047,-6.75 -6.751,-6.75c-3.703,0 -6.75,3.047 -6.75,6.75c0,3.704 3.047,6.751 6.75,6.751Z'></path>
                    </svg>
                    <span class="open-search__caption">{{ __('Search') }}</span>
                    <button type="button" class="btn btn--outline-primary btn--sm open-search__btn" data-action="open-search">
                        Ctrl + K
                    </button>
                </span>
            </div>
            <button class="btn btn--outline-dark btn--sm btn--icon display--none:md" data-action="sidebar-close" aria-label="Close navigation" aria-expanded="false">
                <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2.5' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                    <line x1='18' y1='6' x2='6' y2='18'></line>
                    <line x1='6' y1='6' x2='18' y2='18'></line>
                </svg>
            </button>
        </div>
        <div class="app-sidebar__body">
            <div class="block-navigation" data-item="navigation-block">
                <nav class="block-navigation__menu block-navigation__menu--breakout" data-state="open">
                    <ul>
                        <li>
                            <a href="{{ URL::route('root.dashboard') }}" aria-current="page">
                                <x-root::icon name="home" />
                                {{ __('Dashboard') }}
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="block-navigation" data-item="navigation-block">
                <h3 class="block-navigation__title">
                    Shop
                    <button class="btn btn--light btn--sm btn--icon block-navigation__toggle" aria-expanded="true" aria-label="Toggle shop navigation" data-action="block-navigation-toggle">
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <polyline points='18 15 12 9 6 15'></polyline>
                        </svg>
                    </button>
                </h3>
                <nav class="block-navigation__menu block-navigation__menu--breakout" data-state="open">
                    <ul>
                        <li>
                            <a href="/products/">
                                <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                                    <path d='M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z'></path>
                                    <line x1='3' y1='6' x2='21' y2='6'></line>
                                    <path d='M16 10a4 4 0 0 1-8 0'></path>
                                </svg>
                                Products
                            </a>
                        </li>
                        <li>
                            <a href="/orders/">
                                <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                                    <circle cx='9' cy='21' r='1'></circle>
                                    <circle cx='20' cy='21' r='1'></circle>
                                    <path d='M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6'></path>
                                </svg>
                                Orders
                            </a>
                        </li>
                        <li>
                            <a href="/customers/">
                                <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                                    <path d='M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2'></path>
                                    <circle cx='9' cy='7' r='4'></circle>
                                    <path d='M23 21v-2a4 4 0 0 0-3-3.87'></path>
                                    <path d='M16 3.13a4 4 0 0 1 0 7.75'></path>
                                </svg>
                                Customers
                            </a>
                        </li>
                        <li>
                            <a href="/categories/">
                                <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                                    <polyline points='21 8 21 21 3 21 3 8'></polyline>
                                    <rect x='1' y='3' width='22' height='5'></rect>
                                    <line x1='10' y1='12' x2='14' y2='12'></line>
                                </svg>
                                Categories
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="block-navigation" data-item="navigation-block">
                <h3 class="block-navigation__title">
                    Pages
                    <button class="btn btn--light btn--sm btn--icon block-navigation__toggle" aria-expanded="false" aria-label="Toggle shop navigation" data-action="block-navigation-toggle">
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <polyline points='18 15 12 9 6 15'></polyline>
                        </svg>
                    </button>
                </h3>
                <nav class="block-navigation__menu block-navigation__menu--breakout" data-state="closed">
                    <ul>
                        <li>
                            <a href="/sign-in/">
                                Sign In
                            </a>
                        </li>
                        <li>
                            <a href="/sign-up/">
                                Sign Up
                            </a>
                        </li>
                        <li>
                            <a href="/password-reset/">
                                Password Reset
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </aside>
</div>
