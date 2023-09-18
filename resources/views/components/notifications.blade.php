<div class="app-notification" x-data="notifications('{{ $url }}')" x-on:click.outside="open = false">
    <button class="btn btn--light btn--icon btn--counter app-notification__toggle" x-bind:aria-expanded="open" x-on:click="open = ! open" aria-label="{{ __('Notifications') }}">
        <x-root::icon name="notifications" class="btn__icon" />
        <span class="btn__counter" x-text="unread"></span>
    </button>
    <div class="context-menu">
        <div class="app-notification__header">
            <h2 class="app-notification__title">{{ __('Notifications') }}</h2>
            <button type="button" class="btn btn--sm btn--icon btn--light" aria-label="{{ __('Close') }}" x-on:click="open = false">
                <x-root::icon name="close" class="btn__icon" />
            </button>
        </div>
        <div class="app-notification__list">
            <div class="notification-card notification-card--unread">
                <div class="notification-card__icon notification-card__icon--info">
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                        <circle cx='9' cy='21' r='1'></circle>
                        <circle cx='20' cy='21' r='1'></circle>
                        <path d='M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6'></path>
                    </svg>
                </div>
                <div class="notification-card__caption">
                    <h3 class="notification-card__title">
                        <a href="#" class="notification-card__link">
                            Important Weekly Team Meeting
                        </a>
                    </h3>
                    <p>August 9, 2023 12:00</p>
                </div>
            </div>
        </div>
        {{-- <a href="#" class="btn btn--primary btn--block">View all notifications</a> --}}
    </div>
</div>

{{-- Script --}}
@pushOnce('scripts')
    {{
        Vite::withEntryPoints('resources/js/notifications.js')
            ->useBuildDirectory('vendor/root/build')
            ->useHotFile(public_path('vendor/root/hot'))
    }}
@endpushOnce
