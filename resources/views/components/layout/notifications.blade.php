<div class="app-notification" x-data="notifications('{{ $url }}')" x-on:click.outside="open = false">
    <button
        class="btn btn--light btn--icon btn--counter app-notification__toggle"
        aria-label="{{ __('Notifications') }}"
        x-bind:aria-expanded="open"
        x-on:click="open = ! open"
    >
        <x-root::icon name="notifications" class="btn__icon" />
        <template x-if="unread > 0">
            <span class="btn__counter" x-text="unread"></span>
        </template>
    </button>
    <div class="context-menu" x-bind:class="{ 'is-open': open }">
        <div class="app-notification__header">
            <h2 class="app-notification__title">{{ __('Notifications') }}</h2>
            <button
                type="button"
                class="btn btn--sm btn--icon btn--light"
                aria-label="{{ __('Close') }}"
                x-on:click="open = false"
            >
                <x-root::icon name="close" class="btn__icon" />
            </button>
        </div>
        <div class="app-notification__list">
            <template x-for="notification in notifications" x-bind:key="notification.id">
                <div
                    class="notification-card"
                    x-bind:class="{
                        'notification-card--unread': ! notification.is_read,
                        'notification-card--read': notification.is_read,
                    }"
                >
                    <div class="notification-card__caption">
                        <h3 class="notification-card__title">
                            <a
                                href="#"
                                class="notification-card__link"
                                x-text="notification.subject"
                                x-on:click.prevent="markAsRead(notification)"
                            ></a>
                        </h3>
                        <p x-text="notification.formatted_created_at"></p>
                    </div>
                </div>
            </template>
        </div>
        <template x-if="unread > 0">
            <button type="button" class="btn btn--primary btn--block">
                {{ __('Mark all as read') }}
            </button>
        </template>
    </div>
</div>
