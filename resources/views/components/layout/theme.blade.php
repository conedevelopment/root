<div
    class="theme-switcher"
    data-theme-mode="{{ $theme }}"
    x-data="theme()"
    x-bind:data-theme-mode="theme"
>
    <button
        type="button"
        class="btn btn--outline-dark btn--sm btn--icon theme-switcher__system-mode"
        aria-label="Switch to light mode"
        x-on:click="change('light')"
    >
        <x-root::icon name="light-mode" class="btn__icon" />
    </button>
    <button
        type="button"
        class="btn btn--outline-dark btn--sm btn--icon theme-switcher__light-mode"
        aria-label="Switch to dark mode"
        x-on:click="change('dark')"
    >
        <x-root::icon name="dark-mode" class="btn__icon" />
    </button>
    <button
        type="button"
        class="btn btn--outline-dark btn--sm btn--icon theme-switcher__dark-mode"
        aria-label="Switch to system mode"
        x-on:click="change('system')"
    >
        <x-root::icon name="system-mode" class="btn__icon" />
    </button>
</div>
