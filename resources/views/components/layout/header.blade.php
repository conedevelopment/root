<header class="app-header">
    <div class="app-header__inner">
        <div class="app-header__column">
            <a class="app-header__logo" href="{{ URL::route('root.dashboard') }}" aria-label="{{ Config::get('app.name') }}">
                <img src="{{ URL::asset('vendor/root/img/root-logo.svg') }}" alt="">
            </a>
            <div class="app-header__actions app-header__actions--secondary">
                <button class="btn btn--outline-dark btn--sm btn--icon display--none:md" data-action="sidebar-open" aria-label="Open navigation" aria-expanded="false">
                    <svg aria-hidden='true' focusable='false' height='24' style='fill:currentColor;' version='1.1' viewBox='0 0 24 24' width='24' xml:space='preserve' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                        <path d='M1.016,20.13c-0.288,-0 -0.529,-0.098 -0.724,-0.294c-0.195,-0.196 -0.292,-0.439 -0.292,-0.729c0,-0.289 0.097,-0.53 0.292,-0.722c0.195,-0.192 0.436,-0.288 0.724,-0.288l15.582,0c0.288,0 0.529,0.098 0.724,0.294c0.195,0.196 0.292,0.439 0.292,0.728c0,0.29 -0.097,0.531 -0.292,0.723c-0.195,0.192 -0.436,0.288 -0.724,0.288l-15.582,-0Zm0,-7.182c-0.288,0 -0.529,-0.098 -0.724,-0.293c-0.195,-0.196 -0.292,-0.439 -0.292,-0.729c0,-0.289 0.097,-0.53 0.292,-0.722c0.195,-0.192 0.436,-0.288 0.724,-0.288l11.517,0c0.288,0 0.529,0.098 0.724,0.294c0.195,0.196 0.292,0.439 0.292,0.728c0,0.29 -0.097,0.531 -0.292,0.723c-0.195,0.191 -0.436,0.287 -0.724,0.287l-11.517,0Zm0,-7.045c-0.288,-0 -0.529,-0.098 -0.724,-0.294c-0.195,-0.196 -0.292,-0.439 -0.292,-0.728c0,-0.29 0.097,-0.531 0.292,-0.723c0.195,-0.192 0.436,-0.288 0.724,-0.288l15.582,0c0.288,0 0.529,0.098 0.724,0.294c0.195,0.196 0.292,0.439 0.292,0.729c0,0.289 -0.097,0.53 -0.292,0.722c-0.195,0.192 -0.436,0.288 -0.724,0.288l-15.582,-0Zm18.461,6.063l4.234,4.234c0.203,0.204 0.299,0.441 0.288,0.712c-0.011,0.271 -0.118,0.508 -0.322,0.711c-0.203,0.203 -0.446,0.305 -0.728,0.305c-0.282,-0 -0.525,-0.102 -0.728,-0.305l-4.946,-4.946c-0.203,-0.203 -0.305,-0.44 -0.305,-0.711c0,-0.271 0.102,-0.508 0.305,-0.711l4.946,-4.946c0.203,-0.203 0.446,-0.305 0.728,-0.305c0.282,0 0.525,0.102 0.728,0.305c0.204,0.204 0.305,0.446 0.305,0.729c0,0.282 -0.101,0.525 -0.305,0.728l-4.2,4.2Z'></path>
                    </svg>
                </button>
                <button class="btn btn--outline-dark btn--sm btn--icon display--none:md" aria-label="Open search">
                    <svg aria-hidden='true' focusable='false' height='24' role='img' style='fill: currentColor' viewBox='0 0 24 24' width='24' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                        <path d='M19.501,9.75c-0,2.152 -0.699,4.14 -1.875,5.752l5.935,5.94c0.585,0.586 0.585,1.537 -0,2.123c-0.586,0.586 -1.538,0.586 -2.124,0l-5.935,-5.939c-1.612,1.181 -3.6,1.875 -5.752,1.875c-5.386,-0 -9.75,-4.364 -9.75,-9.751c0,-5.386 4.364,-9.75 9.75,-9.75c5.387,-0 9.751,4.364 9.751,9.75Zm-9.751,6.751c3.704,-0 6.751,-3.047 6.751,-6.751c-0,-3.703 -3.047,-6.75 -6.751,-6.75c-3.703,0 -6.75,3.047 -6.75,6.75c0,3.704 3.047,6.751 6.75,6.751Z'></path>
                    </svg>
                </button>
                <div class="theme-switcher" id="theme-switcher">
                    <button class="btn btn--outline-dark btn--sm btn--icon theme-switcher__system-mode" aria-label="Switch to light mode" data-action="light">
                        <svg aria-hidden='true' focusable='false' height='24' role='img' style='fill: currentColor' viewBox='0 0 24 24' width='24' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <path d='M21,12c0,-4.969 -4.031,-9 -9,-9l0,18c4.969,0 9,-4.031 9,-9Zm3,0c0,6.628 -5.372,12 -12,12c-6.628,0 -12,-5.372 -12,-12c0,-6.628 5.372,-12 12,-12c6.628,0 12,5.372 12,12Z'></path>
                        </svg>
                    </button>
                    <button class="btn btn--outline-dark btn--sm btn--icon theme-switcher__light-mode" aria-label="Switch to dark mode" data-action="dark">
                        <svg aria-hidden='true' focusable='false' height='24' role='img' style='fill: currentColor' viewBox='0 0 24 24' width='24' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <path d='M5.509,3.917l-0.425,-0.426c-0.426,-0.425 -1.113,-0.415 -1.528,0l-0.01,0.011c-0.426,0.425 -0.426,1.113 -0,1.527l0.425,0.426c0.426,0.425 1.102,0.425 1.527,-0l0.011,-0.011c0.426,-0.415 0.426,-1.113 0,-1.527Zm-3.316,6.938l-1.113,-0c-0.6,-0 -1.08,0.48 -1.08,1.08l-0,0.011c-0,0.6 0.48,1.08 1.08,1.08l1.102,-0c0.611,0.011 1.091,-0.469 1.091,-1.069l-0,-0.011c-0,-0.611 -0.48,-1.091 -1.08,-1.091Zm9.818,-10.855l-0.011,-0c-0.61,-0 -1.09,0.48 -1.09,1.08l-0,1.047c-0,0.6 0.48,1.08 1.08,1.08l0.01,0c0.611,0.011 1.091,-0.469 1.091,-1.069l0,-1.058c0,-0.6 -0.48,-1.08 -1.08,-1.08Zm8.444,3.502c-0.425,-0.426 -1.112,-0.426 -1.538,-0.011l-0.425,0.426c-0.426,0.425 -0.426,1.112 -0,1.527l0.011,0.011c0.425,0.425 1.112,0.425 1.527,-0l0.425,-0.426c0.426,-0.425 0.426,-1.102 0,-1.527Zm-1.974,16.473l0.425,0.426c0.426,0.425 1.113,0.425 1.538,-0c0.426,-0.426 0.426,-1.113 0,-1.538l-0.425,-0.426c-0.426,-0.425 -1.113,-0.414 -1.527,0c-0.437,0.436 -0.437,1.113 -0.011,1.538Zm2.247,-8.04l0,0.011c0,0.6 0.48,1.08 1.08,1.08l1.102,-0c0.6,-0 1.08,-0.48 1.08,-1.08l-0,-0.011c-0,-0.6 -0.48,-1.08 -1.08,-1.08l-1.102,-0c-0.6,-0 -1.08,0.48 -1.08,1.08Zm-8.728,-6.535c-3.611,0 -6.545,2.935 -6.545,6.546c-0,3.611 2.934,6.546 6.545,6.546c3.612,-0 6.546,-2.935 6.546,-6.546c0,-3.611 -2.934,-6.546 -6.546,-6.546Zm-0.01,18.492l0.01,-0c0.6,-0 1.08,-0.48 1.08,-1.08l0,-1.048c0,-0.6 -0.48,-1.08 -1.08,-1.08l-0.01,0c-0.6,0 -1.08,0.48 -1.08,1.08l-0,1.048c-0,0.6 0.48,1.08 1.08,1.08Zm-8.444,-3.502c0.425,0.425 1.112,0.425 1.538,-0l0.425,-0.426c0.426,-0.425 0.415,-1.112 0,-1.527l-0.011,-0.011c-0.425,-0.425 -1.112,-0.425 -1.538,0l-0.425,0.426c-0.415,0.436 -0.415,1.112 0.011,1.538Z'></path>
                        </svg>
                    </button>
                    <button class="btn btn--outline-dark btn--sm btn--icon theme-switcher__dark-mode" aria-label="Switch to system mode" data-action="system">
                        <svg aria-hidden='true' focusable='false' height='24' role='img' style='fill: currentColor;' viewBox='0 0 24 24' width='24' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <path d='M13.684,-0c-6.616,-0 -11.974,5.373 -11.974,12c0,6.627 5.358,12 11.974,12c3.246,0 6.187,-1.296 8.346,-3.396c0.268,-0.263 0.338,-0.67 0.166,-1.002c-0.171,-0.332 -0.541,-0.52 -0.911,-0.456c-0.525,0.092 -1.06,0.14 -1.612,0.14c-5.191,-0 -9.402,-4.222 -9.402,-9.429c0,-3.525 1.929,-6.595 4.784,-8.212c0.327,-0.188 0.493,-0.563 0.413,-0.927c-0.081,-0.364 -0.392,-0.638 -0.767,-0.67c-0.337,-0.027 -0.675,-0.043 -1.017,-0.043l-0,-0.005Z'></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="app-header__actions">
            <div class="user-menu">
                <img class="user-menu__avatar" src="https://s.gravatar.com/avatar/0e94d9d2f4c98aa745a1c33aa77446d7a3259a267418eddaaff3422affd5a7f2?s=80&r=g" alt="Adam Laki">
                <div class="user-menu__caption">
                    <span class="user-menu__role">Administrator</span>
                    <span class="user-menu__display-name">info@adamlaki.com</span>
                </div>
                <button class="user-menu__toggle" aria-expanded="false" data-action="context" data-context-target="user">
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='open-search__icon'>
                        <polyline points='6 9 12 15 18 9'></polyline>
                    </svg>
                </button>
                <ul class="context-menu" data-state="closed" data-context-id="user">
                    <li>
                        <a href="/account/">Account</a>
                    </li>
                    <li>
                        <a href="/settings/">Settings</a>
                    </li>
                    <li>
                        <a href="/sign-in">Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
