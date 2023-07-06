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
                    <button
                        type="button"
                        class="btn btn--outline-primary btn--sm open-search__btn"
                        data-action="open-search"
                    >
                        Ctrl + K
                    </button>
                </span>
            </div>
            <button
                type="nutton"
                class="btn btn--outline-dark btn--sm btn--icon display--none:md"
                data-action="sidebar-close"
                aria-label="{{ __('Close navigation') }}"
                aria-expanded="false"
            >
                <x-root::icon name="close" class='btn__icon'/>
            </button>
        </div>
        <div class="app-sidebar__body">
            <div class="block-navigation" data-item="navigation-block">
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
                <div class="block-navigation" data-item="navigation-block">
                    <h3 class="block-navigation__title">
                        {{ $group }}
                        <button class="btn btn--light btn--sm btn--icon block-navigation__toggle" aria-expanded="true" aria-label="Toggle shop navigation" data-action="block-navigation-toggle">
                            <x-root::icon name="chevron-down" class='btn__icon'/>
                        </button>
                    </h3>
                    <nav class="block-navigation__menu block-navigation__menu--breakout" data-state="open">
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
